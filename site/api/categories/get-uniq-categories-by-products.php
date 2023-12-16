<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\CategoryRepository as CategoryRepository;
    use repositories\ProductRepository as ProductRepository;

    class GetUniqCategoriesByProductsController extends BaseController
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
            $result = $this->productRepository->getUniqElementsByProducts($_GET, 'category');

            if ($result)
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }   

    }

    GetUniqCategoriesByProductsController::Create()->HandleRequest();
?>
