<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\VendorRepository as VendorRepository;
    use repositories\ProductRepository as ProductRepository;

    class GetUniqVendorsByProductsController extends BaseController
    {
        private VendorRepository $vendorsRepository;
        private ProductRepository $productRepository;

        public function __construct()
        {
            $this->vendorsRepository = new VendorRepository();
            $this->productRepository = new ProductRepository();
        }

        protected function onGet()
        {
            $result = $this->productRepository->getUniqElementsByProducts($_GET, 'category');

            if ($result)
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }   

    }

    GetUniqVendorsByProductsController::Create()->HandleRequest();
?>
