<?php
use repositories\CustomerRepository;
use utils\CoordinateHelper;
include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

use abstraction\BaseController as BaseController;
use repositories\OrderRepository;
use repositories\OrderVendorRepository;
use models\OrderVendor;
use models\OrderVendorStatus;
use repositories\ProductRepository;
use repositories\VendorRepository;

class CreateOrderWithVendorCalcController extends BaseController
{
    private OrderRepository $orderRepository;
    private OrderVendorRepository $orderVendorRepository;
    private ProductRepository $productRepository;
    private VendorRepository $vendorRepository;
    private CustomerRepository $customerRepository;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
        $this->orderVendorRepository = new OrderVendorRepository();
        $this->productRepository = new ProductRepository();
        $this->vendorRepository = new VendorRepository();
        $this->customerRepository = new CustomerRepository();
    }

    protected function onPost()
    {
        $post = json_decode(file_get_contents('php://input'), true);

        try {
            \DbContext::getConnection()->beginTransaction();

            $post["total_price"] = 0;

            $orderId = $this->orderRepository->add($post);
            $order = $this->orderRepository->getById($orderId);

            if (isset($order->products)) {
                $vendorsWithProducts = [];
                $vendorsWithProductsTotalPrice = [];
                foreach (array_keys($order->products) as $productId) {
                    $product = $this->productRepository->getById($productId);

                    if (!$product || !isset($product->vendor_id))
                        continue;

                    $vendorsWithProducts[$product->vendor_id][$productId] = $order->products[$productId];
                    $vendorsWithProductsTotalPrice[$product->vendor_id] = isset($vendorsWithProductsTotalPrice[$product->vendor_id]) ?
                        $vendorsWithProductsTotalPrice[$product->vendor_id] + $order->products[$productId] * $product->price : $order->products[$productId] * $product->price;
                }

                foreach ($vendorsWithProducts as $key => $value) {
                    $newItem = new OrderVendor();
                    $newItem->order_id = $orderId;
                    $newItem->products = $value;
                    $newItem->status = OrderVendorStatus::Created->value;
                    $newItem->vendor_id = $key;
                    $newItem->archive = 0;
                    $newItem->total_price = $vendorsWithProductsTotalPrice[$key];
                    $newItem->distance = 0;
                    
                    $vendor = $this->vendorRepository->get([
                        'id' => $key
                    ]);

                    if ($vendor != null && $order != null && $vendor->coordinates != null && $order->location != null && isset($vendor->coordinates->latitude) && isset($order->location->latitude))
                    {
                        $lat1 = $order->location->latitude;
                        $lon1 = $order->location->longitude;
                        $lat2 = $vendor->coordinates->latitude;
                        $lon2 = $vendor->coordinates->longitude;
                        $newItem->distance = CoordinateHelper::getDistanceBetweenPoints($lat1, $lon1, $lat2, $lon2);
                    }
                    
                    $this->orderVendorRepository->add((array) $newItem);
                }

                $this->orderRepository->updateById([
                    'id' => $orderId,
                    'total_price' => array_sum($vendorsWithProductsTotalPrice)
                ]);
            }
            \DbContext::getConnection()->commit();
        } catch (Exception $e) {
            \DbContext::getConnection()->rollBack();
            die($e);
        }
    }
}

CreateOrderWithVendorCalcController::Create()->HandleRequest();
?>