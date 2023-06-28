<?php
include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

use abstraction\BaseController as BaseController;
use repositories\OrderVendorRepository as OrderVendorRepository;
use repositories\ProductRepository;

class GetOrderVendorsWithDetailController extends BaseController
{
    private OrderVendorRepository $orderVendorRepository;
    private ProductRepository $productRepository;

    public function __construct()
    {
        $this->orderVendorRepository = new OrderVendorRepository();
        $this->productRepository = new ProductRepository();
    }

    protected function onGet()
    {
        $orderVendors = $this->orderVendorRepository->getWithDetails();

        $orderVendorsProducts = [];

        foreach ($orderVendors as $orderVendor)
            $orderVendorsProducts = array_merge($orderVendorsProducts, array_keys($orderVendor['products']));

        $uniqueProductIds = array_unique($orderVendorsProducts);

        $products = $this->productRepository->getAllByIds($uniqueProductIds); 

        $result = [];

        foreach ($orderVendors as $ordersVendorItem) 
        {
            $newArrayItem = [
                'id' => $ordersVendorItem['id'],
                'order_id' => $ordersVendorItem['order_id'],
                'vendor_id' => $ordersVendorItem['vendor_id'],
                'vendor_location' => $ordersVendorItem['vendor_location'],
                'order_date' => $ordersVendorItem['order_date'],
                'status' => $ordersVendorItem['status'],
                'customer_phone' => $ordersVendorItem['customer_phone'],
                'order_location' => $ordersVendorItem['order_location'],
            ];

            if (isset($ordersVendorItem['products'])) 
            {
                foreach ($ordersVendorItem['products'] as $vendorProductId => $vendorProductCount) 
                {
                    $newProduct = [];

                    $newProduct['id'] = $vendorProductId;
                    $newProduct['quantity'] = $vendorProductCount;

                    foreach ($products as $product) 
                    {
                        if ($product->id == $newProduct['id']) 
                        {
                            $newProduct['name'] = $product->name;
                            $newProduct['price'] = $product->price;
                            $newProduct['available'] = $product->quantity_available;
                            break;
                        }
                    }

                    $newArrayItem['products'][] = $newProduct;
                }
            }

            $result[] = $newArrayItem;
        }

        if ($result)
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}

GetOrderVendorsWithDetailController::Create()->HandleRequest();
?>