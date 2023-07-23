<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\ProductRepository;

    class GetProductsWithCountController extends BaseController
    {
        private ProductRepository $productRepository;

        public function __construct()
        {
            $this->productRepository = new ProductRepository();
        }

        protected function onGet()
        {
            $count = $this->productRepository->getCountWithoutLimit($_GET);
            $products = $this->productRepository->getWithDetails($_GET);

            if (isset($_GET['id']) && $products)
                $products = [$products];

            echo json_encode([
                "count" => $count,
                "products" => $products ?? []
            ]);
        }
    }

    GetProductsWithCountController::Create()->HandleRequest();
?>