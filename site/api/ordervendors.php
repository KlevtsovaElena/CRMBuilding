<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\OrderVendorRepository as OrderVendorRepository;

    class OrderVendorsController extends BaseController
    {
        private OrderVendorRepository $orderVendorRepository;

        public function __construct()
        {
            $this->orderVendorRepository = new OrderVendorRepository();
        }

        protected function onGet()
        {
            $result = $this->orderVendorRepository->get($_GET);
            
            if ($result)
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }   

        protected function onPost()
        {
            $post = json_decode(file_get_contents('php://input'), true);

            if (isset($post['id']))
            {
                $this->orderVendorRepository->updateById($post);
                return;
            }

            $this->orderVendorRepository->add($post);
        }

        protected function onDelete()
        {
            if (isset($_GET['id']))
                $this->orderVendorRepository->removeById($_GET);
        }
    }

    OrderVendorsController::Create()->HandleRequest();
?>