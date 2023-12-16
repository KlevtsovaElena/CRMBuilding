<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\BrandRepository as BrandRepository;
    use repositories\ProductRepository as ProductRepository;

    class GetUniqBrandsByProductsController extends BaseController
    {
        private BrandRepository $brandRepository;
        private ProductRepository $productRepository;

        public function __construct()
        {
            $this->brandRepository = new BrandRepository();
            $this->productRepository = new ProductRepository();
        }

        protected function onGet()
        {
            $result = $this->productRepository->getUniqElementsByProducts($_GET, 'brand');

            if ($result)
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }   

    }

    GetUniqBrandsByProductsController::Create()->HandleRequest();
?>
