<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');


    use abstraction\BaseController;
    use repositories\VendorRepository;

    class ChangePriceRateController extends BaseController
    {
        private VendorRepository $vendorRepository;

        public function __construct()
        {
            $this->vendorRepository = new VendorRepository();
        }

        protected function onPost()
        {
            // передаю 3 поля таблицы vendors: id, price_confirmed (цены не утверждены админом), rate (курс доллара поставщика)
            $post = json_decode(file_get_contents('php://input'), true);

            // обновить поле rate в таблице vendors
            // обновить поле price_confirmed в таблице vendors
            if (isset($post['id']))
            {
                $this->vendorRepository->updateById($post);
                return;
            }

            // 2. пересчитать поля price и max_price ВСЕХ товаров указанного поставщика

            
        }
    }

    ChangePriceRateController::Create()->HandleRequest();
?>