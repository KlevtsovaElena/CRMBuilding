<?php
use repositories\ProductRepository;

include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');


use abstraction\BaseController;
use repositories\VendorRepository;

class ChangePriceMassController extends BaseController
{
    private VendorRepository $vendorRepository;
    private ProductRepository $productRepository;

    public function __construct()
    {
        $this->vendorRepository = new VendorRepository();
        $this->productRepository = new ProductRepository();
    }

    protected function onPost()
    {
        // передаю поля: vendor_id, price_change_case (поднять/снизить на %/Сум), price_change_kind(какие/ую цены), products_kind(на какие товары), magnitude
        // раскодируем json 
        $post = json_decode(file_get_contents('php://input'), true);

        // соберём строку set для запроса 
        // в зависимости от price_change_case определим на или во сколько изменяется цена
        if($post['price_change_case'] == 'priceUpPercent') {
            $dif = '*' . 1+($post['magnitude']/100);
        } else if ($post['price_change_case'] == 'priceDownPercent') {
            $dif = '*' . 1-($post['magnitude']/100);
        } else if ($post['price_change_case'] == 'priceUpSoums') { 
            $dif = '+' . $post['magnitude'];
        } else if ($post['price_change_case'] == 'priceDownSoums') {
            $dif = '-' . $post['magnitude'];  
        }

        // разберём, какие цены/цену будем изменять
        // в price_change_kind один вид или два, разделённые ; (price; price_max)
        $priceKind = explode(";", $post['price_change_kind']);
        // строка set
        $post['set_string'] = '';
        foreach($priceKind as $price) { 
            $post['set_string'] = $post['set_string'] . ' p.' . $price . ' = ' . 'p.' . $price . $dif .',';
        }
        $post['set_string'] = rtrim($post['set_string'], ', ');
        
        // разберём цены на какие товары будем изменять 
        // в products_kind категория, бренд или оба (разделённые ;) или ни одного ('') (category_id=1; brand_id=2)
        if (isset($post['products_kind'])) {
            $productsKind = explode(";", $post['products_kind']);
        } else {
            $productsKind = [];
        }

        // строка where
        $post['where_string'] = 'WHERE vendor_id=' . $post['vendor_id'] ;

        if ($productsKind <> []) {
            foreach($productsKind as $products) { 
                $post['where_string'] = $post['where_string'] . ' AND ' . $products;
            }
        }
        
        // сделаем запрос на изменение цен
        try {
            $this->productRepository->updatePriceMassByVendor($post);
            echo json_encode([
                "success" => true,
                "message" => 'Запрос выполнен!'
            ],
            JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            echo json_encode([
                "success" => false,
                "message" => 'Ошибка! Проверьте, чтобы после уменьшения цен они не стали минусовыми!'
            ],
            JSON_UNESCAPED_UNICODE); 
        } 

    }
}

ChangePriceMassController::Create()->HandleRequest();
?>