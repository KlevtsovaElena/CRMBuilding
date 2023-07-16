<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use models\Vendors;
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
                        'phone' => $vendor[0]->phone,
                        'email' => $vendor[0]->email,
                        'role' => $vendor[0]->role,
                        'coordinates' => [
                            'latitude' => $vendor[0]->coordinates->latitude,
                            'longitude' => $vendor[0]->coordinates->longitude,
                        ],
                        'role' => $vendor[0]->role
                    ],
                ];
                echo json_encode($response,  JSON_UNESCAPED_UNICODE);
            }

            // // если есть, то формируем и записываем токен и возвращаем его на фронт
            // // сформируем токен
            // $id = $vendor[0]->id;
            // $timeToken = time();
            // $timeStr = (string)$timeToken;
            // $timeStr = base_convert($timeStr, 10, 16);
            // $token = crypt($timeStr . $vendor[0]->hash_string, 'token');

            // // подготовим данные
            // $post=[];
            // $post['id'] =  $id;
            // $post['token'] = $token;

            // // обновляем токен в базе
            // $this->vendorRepository->updateById($post);

            // // подготовим ответ
            // $response = [
            //     'success' => true,
            //     'token' => $token,
            //     'id' => $id,
            // ];
            // echo json_encode($response,  JSON_UNESCAPED_UNICODE);

        }
    }

    CheckController::Create()->HandleRequest();
?>