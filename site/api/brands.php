<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\BrandRepository as BrandRepository;

    class BrandsController extends BaseController
    {
        private BrandRepository $brandsRepository;

        public function __construct()
        {
            $this->brandsRepository = new BrandRepository();
        }

        protected function onGet()
        {
            $result = $this->brandsRepository->get($_GET);
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }   

        protected function onPost()
        {
            $post = json_decode(file_get_contents('php://input'), true);

            if (isset($post['id']))
            {
                $this->brandsRepository->update($post);
                return;
            }

            $this->brandsRepository->add($post);
        }

        protected function onDelete()
        {
            if (isset($_GET['id']))
                $this->brandsRepository->removeById($_GET);
        }
    }

    BrandsController::Create()->HandleRequest();
?>