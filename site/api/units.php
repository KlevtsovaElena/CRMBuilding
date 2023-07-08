<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\UnitRepository as UnitRepository;

    class UnitsController extends BaseController
    {
        private UnitRepository $unitRepository;

        public function __construct()
        {
            $this->unitRepository = new UnitRepository();
        }

        protected function onGet()
        {
            $result = $this->unitRepository->get($_GET);

            if ($result)
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }

        protected function onPost()
        {
            $post = json_decode(file_get_contents('php://input'), true);

            if (isset($post['id']))
            {
                $this->unitRepository->updateById($post);
                return;
            }

            $this->unitRepository->add($post);
        }

        protected function onDelete()
        {
            if (isset($_GET['id']))
                $this->unitRepository->removeById($_GET);
        }
    }

    UnitsController::Create()->HandleRequest();
?>