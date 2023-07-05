<?php

    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\VendorRepository;

    class VendorsController extends BaseController
    {
        private VendorRepository $vendorRepository;

        public function __construct()
        {
            $this->vendorRepository = new VendorRepository();
        }

        protected function onGet()
        {
            $result = $this->vendorRepository->get($_GET);
            
            if ($result)
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }

        protected function onPost()
        {        
            $post = json_decode(file_get_contents('php://input'), true);

            if (isset($post['id']))
            {
                $this->vendorRepository->updateById($post);
                return;
            }

            $post['date_reg'] = time();
            $this->vendorRepository->add($post);

            $linkBot = 'https://t.me/KlevtsovaBot2Go_bot?start=' . $post['unique_id'];
            $response = [
                'linkBot' => $linkBot,
                'login' => $post['email'],
                'tempPass' => $post['temp_password'],
            ];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        }

        protected function onDelete()
        {
            if (isset($_GET['id']))
                $this->vendorRepository->removeById($_GET);
        }

 
    }

    VendorsController::Create()->HandleRequest();
?>