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
            $result = $this->categoryRepository->get($_GET);

            if ($result)
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }

        protected function onPost()
        {
            $post = json_decode(file_get_contents('php://input'), true);

            if (isset($post['id']))
            {
                $this->categoryRepository->updateById($post);
                return;
            }

            $this->categoryRepository->add($post);
        }

        protected function onDelete()
        {
            if (isset($_GET['id']))
                $this->categoryRepository->removeById($_GET);
        }
    }

    CategoriesController::Create()->HandleRequest();
?>