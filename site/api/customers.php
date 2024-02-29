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

            var_dump($post);

            if (isset($post['id']) && $post['id'] != 0)
            {
                $this->customerRepository->updateById($post);
                $db = \DbContext::getConnection();
                $db->query("UPDATE customers SET step = " . (int)$post['step']. " WHERE id = " . (int)$post['id']);
                $db->query("UPDATE customers SET language = '" . $post['language'] ."' WHERE id = " . (int)$post['id']);
                return;
            }

            if (isset($post['tg_id']) && $this->customerRepository->getByTgId($post['tg_id']) != null)
            {
                $this->customerRepository->updateByTgId($post);
                return;
            }

            if ($this->customerRepository->getByTgId($post['tg_id']) == null) {
                $db = \DbContext::getConnection();
                unset($post['id']);
                $id = $this->customerRepository->add($post);
                $db->query("UPDATE customers SET step = 2 WHERE id = " .$id);
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