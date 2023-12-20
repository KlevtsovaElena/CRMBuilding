<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\CategoryRepository as CategoryRepository;
    use repositories\ProductRepository as ProductRepository;

    class GetAllByExistProductsController extends BaseController
    {
        private CategoryRepository $categoriesRepository;
        private ProductRepository $productRepository;

        public function __construct()
        {
            $this->categoriesRepository = new CategoryRepository();
            $this->productRepository = new ProductRepository();
        }

        protected function onGet()
        {
            $get = $_GET;
            $get['deleted'] = '0'; 
            $get['is_active'] = '1';
            $get['is_confirm'] = '1';
            $get['vendor_deleted'] = '0';
            $get['vendor_active'] = '1';
            $get['price_confirmed'] = '1';
            $get['city_deleted'] = '0';
            $get['city_active'] = '1';
            $get['orderby'] = 'category_name:asc';

            $result = $this->productRepository->getUniqElementsByProducts($get, 'category');

            if ($result)

                for ($i = 0; $i < count($result); $i ++) {
                    $result[$i]['id'] = $result[$i]['category_id'];
                }
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }   

    }

    GetAllByExistProductsController::Create()->HandleRequest();
?>


<?php
    // include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    // use abstraction\BaseController as BaseController;
    // use repositories\CategoryRepository;

    // class GetAllByExistProductsController extends BaseController
    // {
    //     private CategoryRepository $categoryRepository;

    //     public function __construct()
    //     {
    //         $this->categoryRepository = new CategoryRepository();
    //     }

    //     protected function onGet()
    //     {
    //         $response = $this->categoryRepository->getAllByExistProducts();
    //         echo json_encode($response);
    //     }
    // }

    // GetAllByExistProductsController::Create()->HandleRequest();
?>