<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\OrderRepository as OrderRepository;

    class OrdersController extends BaseController
    {
        private OrderRepository $orderRepository;

        public function __construct()
        {
            $this->orderRepository = new OrderRepository();
        }

        protected function onGet()
        {
            $result = $this->orderRepository->get($_GET);
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }   

        protected function onPost()
        {
            $post = json_decode(file_get_contents('php://input'), true);

            if (isset($post['id']))
            {
                $this->orderRepository->update($post);
                return;
            }

            $this->orderRepository->add($post);
        }

        protected function onDelete()
        {
            if (isset($_GET['id']))
                $this->orderRepository->removeById($_GET);
        }
    }

    OrdersController::Create()->HandleRequest();
?>