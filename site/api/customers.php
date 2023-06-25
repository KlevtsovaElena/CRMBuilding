<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\CustomerRepository as CustomerRepository;

    class CustomersController extends BaseController
    {
        private CustomerRepository $customerRepository;

        public function __construct()
        {
            $this->customerRepository = new CustomerRepository();
        }

        protected function onGet()
        {
            $result = $this->customerRepository->get($_GET);
            
            if ($result)
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }   

        protected function onPost()
        {
            $post = json_decode(file_get_contents('php://input'), true);

            if (isset($post['id']))
            {
                $this->customerRepository->updateById($post);
                return;
            }

            if (isset($post['tg_id']) && $this->customerRepository->getByTgId($post['tg_id']) != null)
            {
                $this->customerRepository->updateByTgId($post);
                return;
            }

            $this->customerRepository->add($post);
        }

        protected function onDelete()
        {
            if (isset($_GET['id']))
                $this->customerRepository->removeById($_GET);
        }
    }

    CustomersController::Create()->HandleRequest();
?>