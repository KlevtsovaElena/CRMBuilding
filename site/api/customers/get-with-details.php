<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\CustomerRepository as CustomerRepository;

    class GetCustomersWithDetailsController extends BaseController
    {
        private CustomerRepository $customerRepository;

        public function __construct()
        {
            $this->customerRepository = new CustomerRepository();
        }

        protected function onGet()
        {
            $customers = $this->customerRepository->getWithDetails($_GET);

            echo json_encode($customers, JSON_UNESCAPED_UNICODE);
        }
    }

    GetCustomersWithDetailsController::Create()->HandleRequest();
?>