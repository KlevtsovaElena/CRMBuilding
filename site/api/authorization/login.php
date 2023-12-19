<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\VendorRepository;

    class LoginController extends BaseController
    {
        private VendorRepository $vendorRepository;

        public function __construct()
        {
            $this->vendorRepository = new VendorRepository();
        }

        protected function onPost()
        {
            $post = $_POST;
            $post['deleted'] = '0';
            $vendor = $this->vendorRepository->getWithDetails($post);

            // проверяем есть ли пользователь с такими логин-пароль
            
            // если нет, то возвращаем на фронт ошибку
            if (!$vendor) {
                $response = [
                    'success' => false,
                    'error' => 'Неверный логин или пароль'
                ];
                echo json_encode($response,  JSON_UNESCAPED_UNICODE);
                return;
            } 

            $vendor = (object)$vendor[0];
            // если есть, то проверим админ ли это
            // если не админ, то он должен быть is_active, город его должен быть активным и неудалённым
            if($vendor->role <> '1') {
                if ($vendor->city_active == '0' || $vendor->city_deleted == '1' || $vendor->is_active == '0') {
                    $response = [
                        'success' => false,
                        'error' => 'Доступ заблокирован'
                    ];
                    echo json_encode($response,  JSON_UNESCAPED_UNICODE);
                    return;
                }
            }

            // если есть, то формируем и записываем токен и возвращаем его на фронт
            // сформируем токен
            $id = $vendor->id;
            $timeToken = time();
            $timeStr = (string)$timeToken;
            $timeStr = base_convert($timeStr, 10, 16);
            $token = crypt($timeStr . $vendor->hash_string, 'token');
            $token = preg_replace("/[^a-zA-Z0-9]/", "", $token);
            // подготовим данные
            $post=[];
            $post['id'] =  $id;
            $post['token'] = $token;

            // обновляем токен в базе
            $this->vendorRepository->updateById($post);

            // подготовим ответ
            $response = [
                'success' => true,
                'token' => $token,
                'id' => $id,
            ];
            echo json_encode($response,  JSON_UNESCAPED_UNICODE);

        }
    }

    LoginController::Create()->HandleRequest();
?>