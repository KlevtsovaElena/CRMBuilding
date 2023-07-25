<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\CategoryRepository;

    class GetAllByExistProductsController extends BaseController
    {
        private CategoryRepository $categoryRepository;

        public function __construct()
        {
            $this->categoryRepository = new CategoryRepository();
        }

        protected function onGet()
        {
            $response = $this->categoryRepository->getAllByExistProducts();
            echo json_encode($response);
        }
    }

    GetAllByExistProductsController::Create()->HandleRequest();
?>