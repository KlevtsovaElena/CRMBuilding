<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');


    use abstraction\BaseController;
    use repositories\VendorRepository;

    class GetVendorsWithDetailController extends BaseController
    {
        private VendorRepository $vendorRepository;

        public function __construct()
        {
            $this->vendorRepository = new VendorRepository();
        }

        protected function onGet()
        {
            $products = $this->vendorRepository->getWithDetails($_GET);

            echo json_encode($products, JSON_UNESCAPED_UNICODE);
        }
    }

    GetVendorsWithDetailController::Create()->HandleRequest();
?>