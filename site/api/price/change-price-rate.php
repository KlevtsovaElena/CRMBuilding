<?php
use repositories\ProductRepository;

include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');


use abstraction\BaseController;
use repositories\VendorRepository;

class ChangePriceRateController extends BaseController
{
    private VendorRepository $vendorRepository;
    private ProductRepository $productRepository;

    public function __construct()
    {
        $this->vendorRepository = new VendorRepository();
        $this->productRepository = new ProductRepository();
    }

    protected function onPost()
    {
        // передаю 3 поля таблицы vendors: id, price_confirmed (цены не утверждены админом), rate (курс доллара поставщика)
        $post = json_decode(file_get_contents('php://input'), true);

        if (isset($post['id'])) {
            try {
                DbContext::getConnection()->beginTransaction();
                // обновить поле rate в таблице vendors
                // обновить поле price_confirmed в таблице vendors
                $this->vendorRepository->updateById($post);

                // 2. пересчитать поля price и max_price ВСЕХ товаров указанного поставщика
                $this->productRepository->updatePriceByVendor($post['id']);

                \DbContext::getConnection()->commit();
            } catch (Exception $e) {
                \DbContext::getConnection()->rollBack();
                die($e);
            }
        }
    }
}

ChangePriceRateController::Create()->HandleRequest();
?>