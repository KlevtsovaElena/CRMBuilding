<?php
    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use models\Vendors;
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
            $vendor = $this->vendorRepository->get($_POST);

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

            // если есть, то формируем и записываем токен и возвращаем его на фронт
            // сформируем токен
            $id = $vendor[0]->id;
            $timeToken = time();
            $timeStr = (string)$timeToken;
            $timeStr = base_convert($timeStr, 10, 16);
            $token = crypt($timeStr . $vendor[0]->hash_string, 'token');

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