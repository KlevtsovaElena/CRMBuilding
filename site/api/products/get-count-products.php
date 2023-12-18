<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\ProductRepository;

    class GetCountProductsController extends BaseController
    {
        private ProductRepository $productRepository;

        public function __construct()
        {
            $this->productRepository = new ProductRepository();
        }

        protected function onGet()
        {
            $count = $this->productRepository->getCountWithDetails($_GET);

            echo $count;
        }
    }

    GetCountProductsController::Create()->HandleRequest();
?>