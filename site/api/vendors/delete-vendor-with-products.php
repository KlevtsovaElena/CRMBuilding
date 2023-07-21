<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\VendorRepository;
    use repositories\ProductRepository;


    class DeleteVendorWithProductsController extends BaseController
    {
        private VendorRepository $vendorRepository;
        private ProductRepository $productRepository;

        const UPDATE_DELETED_PRODUCTS = 'UPDATE `products` SET `deleted`="2" WHERE `vendor_id`=:vendor_id';

        public function __construct()
        {
            $this->vendorRepository = new VendorRepository();
            $this->productRepository = new ProductRepository();
        }

        protected function onPost()
        {

            $post = json_decode(file_get_contents('php://input'), true);

            // делаем удаление в базе поставщика
            $this->vendorRepository->updateById($post);

            $vendor_id = $post['id'];

            // делаем удаление товаров поставщика в базе товаров (со значением 2)
            $query = sprintf(static::UPDATE_DELETED_PRODUCTS);

            $statement = \DbContext::getConnection()->prepare($query);
            $statement->execute(['vendor_id'=>$vendor_id]);
        }

    }

    DeleteVendorWithProductsController::Create()->HandleRequest();
?>