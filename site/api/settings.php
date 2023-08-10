<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\SettingsRepository as SettingsRepository;

    class SettingsController extends BaseController
    {
        private SettingsRepository $settingsRepository;

        public function __construct()
        {
            $this->settingsRepository = new SettingsRepository();
        }

        protected function onGet()
        {
            $result = $this->settingsRepository->get($_GET);

            if ($result)
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }   

        protected function onPost()
        {
            $post = json_decode(file_get_contents('php://input'), true);

            if (isset($post['id']))
            {
                $this->settingsRepository->updateById($post);
                return;
            }

            if (isset($post['name']))
            {
                $existsItem = $this->settingsRepository->get([
                    'name' => $post['name']
                ]);

                if ($existsItem != null)
                {
                    $post['id'] = $existsItem[0]->id;
                    $this->settingsRepository->updateById($post);
                    return;
                }
            }

            $this->settingsRepository->add($post);
        }

        protected function onDelete()
        {
            if (isset($_GET['id']))
                $this->settingsRepository->removeById($_GET);
        }
    }

    SettingsController::Create()->HandleRequest();
?>