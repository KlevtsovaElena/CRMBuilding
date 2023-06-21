<?php
use models\Category;
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\CategoryRepository as CategoryRepository;

    class CategoriesController extends BaseController
    {
        private CategoryRepository $categoryRepository;

        public function __construct()
        {
            $this->categoryRepository = new CategoryRepository();
        }

        protected function onGet()
        {
            $result = isset($_GET['id']) ? $this->categoryRepository->getById($_GET['id']) :
                $this->categoryRepository->getAll();

            if ($result)
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }

        protected function onPost()
        {
            if (!isset($_POST['categoryName']))
                return;

            $category = new Category();
            $category->categoryName = $_POST['categoryName'];

            if (isset($_POST['id']))
            {
                $category->id = $_POST['id'];
                $this->categoryRepository->update($category);
                return;
            }

            $this->categoryRepository->add($category);
        }

        protected function onDelete()
        {
            if (!isset($_GET['id']))
                return;

            $this->categoryRepository->removeById($_GET['id']);
        }
    }

    CategoriesController::Create()->HandleRequest();
?>