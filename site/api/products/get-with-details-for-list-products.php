<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');


    use abstraction\BaseController;
    use repositories\ProductRepository;

    class GetProductsWithDetailController extends BaseController
    {
        private ProductRepository $productRepository;

        public function __construct()
        {
            $this->productRepository = new ProductRepository();
        }

        protected function onGet()
        {
            $products = $this->productRepository->getWithDetailsForFront($_GET);

            echo json_encode($products, JSON_UNESCAPED_UNICODE);
        }
    }

    GetProductsWithDetailController::Create()->HandleRequest();
?>