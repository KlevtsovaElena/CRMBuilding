<?php
use repositories\OrderVendorRepository;
use repositories\ProductRepository;
use repositories\VendorRepository;
use utils\SqlHelper;

include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

use abstraction\BaseController as BaseController;

class GetProductsSalesByPeriodController extends BaseController
{
    private OrderVendorRepository $orderVendorsRepository;
    private ProductRepository $productRepository;
    private VendorRepository $vendorRepository;

    public function __construct()
    {
        $this->orderVendorsRepository = new OrderVendorRepository();
        $this->productRepository = new ProductRepository();
        $this->vendorRepository = new VendorRepository();
    }

    protected function onGet()
    {
        $deliveredOrderVendors = $this->orderVendorsRepository->getDeliveredWithDetails($_GET);
        $orderVendorsWithProductData = [];
        $orderVendorsProducts = [];

        //соберем массив из категорий, доступных оптовику
        $categories = [];

        if (isset($_GET['wholesaler_id'])) {

            //достаем все данные по данному отповику, исходя из его id, переданного в запросе
            $wholesaler = $this->vendorRepository->get([
                'id' => $_GET['wholesaler_id']
            ]);
            //print_r($wholesaler);

            try {
                DbContext::getConnection()->beginTransaction();
                //достаем категории оптовика, исходя из полученных выше всех данных оптовика
                $categories = $wholesaler->categories;
                //print_r($categories);

                \DbContext::getConnection()->commit();
            } catch (Exception $e) {
                \DbContext::getConnection()->rollBack();
                die($e);
            }
        }

        foreach ($deliveredOrderVendors as $orderVendor) {
            if (!isset($orderVendorsWithProductData[$orderVendor['vendor_id']])) {
                $orderVendorsWithProductData[$orderVendor['vendor_id']] = [
                    'vendor_id' => $orderVendor['vendor_id'],
                    'vendor_city' => $orderVendor['vendor_city'],
                    'vendor_name' => $orderVendor['vendor_name'],
                    'products' => []
                ];
            }
            $currentProducts = json_decode($orderVendor['vendor_products']);
            //print_r($currentProducts);

            foreach ($currentProducts as $productId => $productCount) {
                if (!isset($orderVendorsWithProductData[$orderVendor['vendor_id']]['products'][$productId])) {
                    $orderVendorsWithProductData[$orderVendor['vendor_id']]['products'][$productId]['quantity'] = 0;
                }

                $orderVendorsWithProductData[$orderVendor['vendor_id']]['products'][$productId]['quantity'] += $productCount;
                $orderVendorsProducts[$productId] = 1;
            }
        }

        $existsProductInfo = $this->productRepository->getAllByIdsWithNameFront(array_keys($orderVendorsProducts));

        foreach ($orderVendorsWithProductData as $key => $value) {
            foreach ($orderVendorsWithProductData[$key]['products'] as $id => $value) {
                foreach ($existsProductInfo as $existProduct) {
                    if ($existProduct['id'] == $id) {
                                $orderVendorsWithProductData[$key]['products'][$id]['price'] = $existProduct['price'];
                                $orderVendorsWithProductData[$key]['products'][$id]['total_price'] = $orderVendorsWithProductData[$key]['products'][$id]['quantity'] * $orderVendorsWithProductData[$key]['products'][$id]['price'];
                                $orderVendorsWithProductData[$key]['products'][$id]['name'] = $existProduct['name_front'];
                                $orderVendorsWithProductData[$key]['products'][$id]['category_id'] = $existProduct['category_id'];
                                break;
                        
                    }
                }
            }
        }

        $result = [];

        foreach ($orderVendorsWithProductData as $orderVendorsWithProductDataItem) {
            foreach ($orderVendorsWithProductDataItem['products'] as $orderVendorsWithProductDataItemProduct) {
                $result[] = [
                    'vendor_id' => $orderVendorsWithProductDataItem['vendor_id'],
                    'vendor_city' => $orderVendorsWithProductDataItem['vendor_city'],
                    'vendor_name' => $orderVendorsWithProductDataItem['vendor_name'],
                    'name' => $orderVendorsWithProductDataItemProduct['name'],
                    'quantity' => $orderVendorsWithProductDataItemProduct['quantity'],
                    'price' => $orderVendorsWithProductDataItemProduct['price'],
                    'total_price' => $orderVendorsWithProductDataItemProduct['total_price'],
                    'category_id' => $orderVendorsWithProductDataItemProduct['category_id'],
                ];
            }
        }

        $orderByParams = SqlHelper::getAllOrderByParams($_GET);
        $result1 = SqlHelper::sortArray($result, $orderByParams);

        //print_r($result1[0]);

        //пересоберем массив только с теми категориями, которые доступны данному оптовику
        $result = [];
        for ($i = 0; $i < count($result1); $i++) {
            foreach ($categories as $key => $value) {
                if ($result1[$i]['category_id'] == $key) {
                    //print_r($result1[$i]);
                    
                    array_push($result, $result1[$i]);
                }
            }
            
        }
        //print_r($result);

        echo json_encode([
            'count' => count($result),
            'products' => array_slice($result, $_GET['offset'] ?? 0, $_GET['limit'] ?? null)
        ], JSON_UNESCAPED_UNICODE);
    }
}

GetProductsSalesByPeriodController::Create()->HandleRequest();
?>