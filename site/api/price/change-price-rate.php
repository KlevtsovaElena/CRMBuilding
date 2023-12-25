<?php
use repositories\ProductRepository;

include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');


use abstraction\BaseController;
use repositories\VendorRepository;

class ChangePriceRateController extends BaseController
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
        // передаю 3 поля таблицы vendors: id, price_confirmed (цены не утверждены поставщиком), rate (курс доллара поставщика)
        $post = json_decode(file_get_contents('php://input'), true);

        $get['vendor_id'] = $post['id'];
        $get['is_active'] = '1';
        $get['is_confirm'] = '1';
        $get['deleted'] = '0';
        $get['vendor_deleted'] = '0';
        $get['vendor_active'] = '1';
        $get['city_deleted'] = '0';
        $get['city_active'] = '1';

        if (isset($post['id'])) {
            try {
                DbContext::getConnection()->beginTransaction();
                $count = $this->productRepository->getCountWithoutLimit($get);

                // обновить поле rate в таблице vendors
                // обновить поле price_confirmed в таблице vendors
                $this->vendorRepository->updateById($post);

                // 2. пересчитать поля price и max_price ВСЕХ товаров указанного поставщика
                $this->productRepository->updatePriceByVendor($post['id']);

                echo json_encode([
                    "success" => true,
                    "message" => 'Запрос выполнен!',
                    "count" => $count
                ],
                JSON_UNESCAPED_UNICODE);

                \DbContext::getConnection()->commit();
            } catch (Exception $e) {
                echo json_encode([
                    "success" => false
                ],
                JSON_UNESCAPED_UNICODE); 
                \DbContext::getConnection()->rollBack();
                die($e);
            }
        }
    }
}

ChangePriceRateController::Create()->HandleRequest();
?>