<?php
use repositories\OrderVendorRepository;
use repositories\ProductRepository;
use utils\SqlHelper;

include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

use abstraction\BaseController as BaseController;

class GetProductsSalesByPeriodController extends BaseController
{
    private OrderVendorRepository $orderVendorsRepository;
    private ProductRepository $productRepository;

    public function __construct()
    {
        $this->orderVendorsRepository = new OrderVendorRepository();
        $this->productRepository = new ProductRepository();
    }

    protected function onGet()
    {
        $deliveredOrderVendors = $this->orderVendorsRepository->getDeliveredWithDetails($_GET);
        $orderVendorsWithProductData = [];
        $orderVendorsProducts = [];

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
        $result = SqlHelper::sortArray($result, $orderByParams);

        echo json_encode([
            'count' => count($result),
            'products' => array_slice($result, $_GET['offset'] ?? 0, $_GET['limit'] ?? null)
        ], JSON_UNESCAPED_UNICODE);
    }
}

GetProductsSalesByPeriodController::Create()->HandleRequest();
?>