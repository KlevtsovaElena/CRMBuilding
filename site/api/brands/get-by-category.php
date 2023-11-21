<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\BrandRepository;

    class GetBrandsByCategory extends BaseController
    {
        private BrandRepository $brandsRepository;

        public function __construct()
        {
            $this->brandsRepository = new BrandRepository();
        }

        protected function onGet()
        {
            if (!isset($_GET['category_id']))
                return;

            $response = $this->brandsRepository->getByCategoryId($_GET['category_id']);
            echo json_encode($response);
        }
    }

    GetBrandsByCategory::Create()->HandleRequest();
?>