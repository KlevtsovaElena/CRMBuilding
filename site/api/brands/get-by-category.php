<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\BrandRepository as BrandRepository;
    use repositories\ProductRepository as ProductRepository;

    class GetBrandsByCategory extends BaseController
    {
        private BrandRepository $brandRepository;
        private ProductRepository $productRepository;

        public function __construct()
        {
            $this->brandRepository = new BrandRepository();
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
            $get['city_deleted'] = '0';
            $get['city_active'] = '1';
            $get['orderby'] = 'brand_name:asc';

            $result = $this->productRepository->getUniqElementsByProducts($get, 'brand');

            if ($result)
                for ($i = 0; $i < count($result); $i ++) {
                    $result[$i]['id'] = $result[$i]['brand_id'];
                }
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }   

    }

    GetBrandsByCategory::Create()->HandleRequest();
?>




<?php
    // include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    // use abstraction\BaseController as BaseController;
    // use repositories\BrandRepository;

    // class GetBrandsByCategory extends BaseController
    // {
    //     private BrandRepository $brandsRepository;

    //     public function __construct()
    //     {
    //         $this->brandsRepository = new BrandRepository();
    //     }

    //     protected function onGet()
    //     {
    //         if (!isset($_GET['category_id']))
    //             return;

    //         $response = $this->brandsRepository->getByCategoryId($_GET['category_id']);
    //         echo json_encode($response);
    //     }
    // }

    // GetBrandsByCategory::Create()->HandleRequest();
?>