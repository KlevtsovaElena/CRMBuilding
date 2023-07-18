<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\OrderVendorRepository as OrderVendorRepository;

    class GetCountController extends BaseController
    {
        private OrderVendorRepository $orderVendorRepository;

        public function __construct()
        {
            $this->orderVendorRepository = new OrderVendorRepository();
        }

        protected function onGet()
        {
            $count = $this->orderVendorRepository->getCountWithDetails($_GET);

            echo json_encode(["count" => $count]);

        }

    }

    GetCountController::Create()->HandleRequest();
?>