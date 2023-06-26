<?php
use models\OrderVendor;
use models\OrderVendorStatus;
use repositories\ProductRepository;
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\OrderRepository;
    use repositories\OrderVendorRepository;

    class CreateOrderWithVendorCalcController extends BaseController
    {
        private OrderRepository $orderRepository;
        private OrderVendorRepository $orderVendorRepository;
        private ProductRepository $productRepository;

        public function __construct()
        {
            $this->orderRepository = new OrderRepository();
            $this->orderVendorRepository = new OrderVendorRepository();
            $this->productRepository = new ProductRepository();
        }  

        protected function onPost()
        {
            $post = json_decode(file_get_contents('php://input'), true);

            try {
                \DbContext::getConnection()->beginTransaction();
                $orderId = $this->orderRepository->add($post);
                $order = $this->orderRepository->getById($orderId);

                if (isset($order->products))
                {
                    $vendorsWithProducts = [];
                    foreach(array_keys($order->products) as $productId)
                    {
                        $product = $this->productRepository->getById($productId);

                        if (!$product || !isset($product->vendor_id))
                            continue;

                        $vendorsWithProducts[$product->vendor_id][$productId] = $order->products[$productId];
                    }

                    foreach($vendorsWithProducts as $key => $value)
                    {
                        $newItem = new OrderVendor();
                        $newItem->order_id = $orderId;
                        $newItem->products = $value;
                        $newItem->status = OrderVendorStatus::Created->value;
                        $newItem->vendor_id = $key;

                        $this->orderVendorRepository->add((array) $newItem);
                    }
                }

                \DbContext::getConnection()->commit();
            } catch(Exception $e) {
                \DbContext::getConnection()->rollBack();
                die($e);
            }
        }
    }

    CreateOrderWithVendorCalcController::Create()->HandleRequest();
?>