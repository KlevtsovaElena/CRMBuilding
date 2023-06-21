<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\BrandRepository as BrandRepository;
    use models\Brand as Brand;

    class BrandsController extends BaseController
    {
        private BrandRepository $brandsRepository;

        public function __construct()
        {
            $this->brandsRepository = new BrandRepository();
        }

        protected function onGet()
        {
            $result = isset($_GET['id']) ? $this->brandsRepository->getById($_GET['id']) :
                $this->brandsRepository->getAll();

            if ($result)
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }   

        protected function onPost()
        {
            if (!isset($_POST['brandName']))
                return;

            $brand = new Brand();
            $brand->brandName = $_POST['brandName'];

            if (isset($_POST['id']))
            {
                $brand->id = $_POST['id'];
                $this->brandsRepository->update($brand);
                return;
            }

            $this->brandsRepository->add($brand);
        }

        protected function onDelete()
        {
            if (!isset($_GET['id']))
                return;

            $this->brandsRepository->removeById($_GET['id']);
        }
    }

    BrandsController::Create()->HandleRequest();
?>