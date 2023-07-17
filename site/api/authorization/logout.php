<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use models\Vendors;
    use repositories\VendorRepository;


    class LogoutController extends BaseController
    {
        private VendorRepository $vendorRepository;

        const UPDATE_QUERY_TOKEN = 'UPDATE `vendors` SET `token`="" WHERE `token`=:token';

        public function __construct()
        {
            $this->vendorRepository = new VendorRepository();
        }

        protected function onPost()
        {

            $token = $_POST['token'];

            $query = sprintf(static::UPDATE_QUERY_TOKEN);

            $statement = \DbContext::getConnection()->prepare($query);
            $statement->execute(['token'=>$token]);
        }

    }

    LogoutController::Create()->HandleRequest();
?>