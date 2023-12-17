<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\CityRepository as CityRepository;
    use repositories\ProductRepository as ProductRepository;

    class GetUniqCitiesByProductsController extends BaseController
    {
        private CityRepository $citiesRepository;
        private ProductRepository $productRepository;

        public function __construct()
        {
            $this->citiesRepository = new CityRepository();
            $this->productRepository = new ProductRepository();
        }

        protected function onGet()
        {
            $result = $this->productRepository->getUniqElementsByProducts($_GET, 'city');

            if ($result)
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }   

    }

    GetUniqCitiesByProductsController::Create()->HandleRequest();
?>
