<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\VendorRepository;

    class CheckController extends BaseController
    {
        private VendorRepository $vendorRepository;

        public function __construct()
        {
            $this->vendorRepository = new VendorRepository();
        }

        protected function onPost()
        {

            $post = [];
            $post['token'] = $_POST['cookie'];

            $vendor = $this->vendorRepository->get($post);

            // проверяем есть ли пользователь с таким токеном
            
            // если нет, то возвращаем на фронт ошибку
            // если есть, то возвращаем на фронт его данные
            if (!$vendor) {
                $response = [
                    'success' => false,
                    'error' => ''
                ];
                echo json_encode($response,  JSON_UNESCAPED_UNICODE);
                return;
            } else {
                $response = [
                    'success' => true,
                    'profile' => [
                        'id' => $vendor[0]->id,
                        'name' => $vendor[0]->name,
                        'city_id' => $vendor[0]->city_id,
                        'tg_id' => $vendor[0]->tg_id,
                        'hash_string' => $vendor[0]->hash_string,
                        'phone' => $vendor[0]->phone,
                        'email' => $vendor[0]->email,
                        'role' => $vendor[0]->role,
                        'coordinates' => $vendor[0]->coordinates
                    ],
                ];
                echo json_encode($response,  JSON_UNESCAPED_UNICODE);
            }

        }
    }

    CheckController::Create()->HandleRequest();
?>