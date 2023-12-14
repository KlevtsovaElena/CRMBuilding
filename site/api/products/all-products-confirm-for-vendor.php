<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\ProductRepository;
    use repositories\VendorRepository;

    class AllProductsConfirmForVendorController extends BaseController
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
            // передаю поля: vendor_id
            // раскодируем json 
            $post = json_decode(file_get_contents('php://input'), true);
            
            // сделаем запрос на одобрение товаров
            try {
                $this->productRepository->updateConfirmProductByVendor($post);
                $count = $this->productRepository->countNotConfirmProductByVendor($post);
                if ($count == 0) {
                    echo json_encode([
                        "success" => true,
                        "message" => 'Запрос выполнен!'
                    ],
                    JSON_UNESCAPED_UNICODE);
                } else {
                    echo json_encode([
                        "success" => true,
                        "message" => 'Запрос выполнен! Товары с корректными ценами одобрены. Неодобренных товаров: ' . $count
                    ],
                    JSON_UNESCAPED_UNICODE); 
                }

            } catch (PDOException $e) {
                echo json_encode([
                    "success" => false,
                    "message" => 'Ошибка!'
                ],
                JSON_UNESCAPED_UNICODE); 
            } 
        }

    }

    AllProductsConfirmForVendorController::Create()->HandleRequest();
?>