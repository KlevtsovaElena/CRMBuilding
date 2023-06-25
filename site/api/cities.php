<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\CityRepository as CityRepository;

    class CitiesController extends BaseController
    {
        private CityRepository $citiesRepository;

        public function __construct()
        {
            $this->citiesRepository = new CityRepository();
        }

        protected function onGet()
        {
            $result = $this->citiesRepository->get($_GET);

            if ($result)
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }   

        protected function onPost()
        {
            $post = json_decode(file_get_contents('php://input'), true);

            if (isset($post['id']))
            {
                $this->citiesRepository->updateById($post);
                return;
            }

            $this->citiesRepository->add($post);
        }

        protected function onDelete()
        {
            if (isset($_GET['id']))
                $this->citiesRepository->removeById($_GET);
        }
    }

    CitiesController::Create()->HandleRequest();
?>