<?php

    include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

    use abstraction\BaseController as BaseController;
    use repositories\VendorRepository;

    class VendorsController extends BaseController
    {
        private VendorRepository $vendorRepository;

        public function __construct()
        {
            $this->vendorRepository = new VendorRepository();
        }

        protected function onGet()
        {
            $result = $this->vendorRepository->get($_GET);
            
            if ($result)
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }

        protected function onPost()
        {        
            $post = json_decode(file_get_contents('php://input'), true);

            // редактирование по id
            if (isset($post['id']))
            {
                $this->vendorRepository->updateById($post);
                return;
            }

            // редактирование по hash (tg_id и очистка хэша)
            if (isset($post['hash_string']))
            {
                $this->vendorRepository->updateByHash($post);
                return;
            }

            // В остальных случаях - новый поставщик
// ВОТ ЗДЕСЬ ПРОВЕРКА НА СУЩЕСТВОВАНИЕ В ТАБЛИЦЕ ЗАПИСИ С УКАЗАННЫМ $post['email']
// если такой уже есть, то вернуть на фронт error = 'поставщик с таким email уже существует' и выйти

            // добавим недостающие поля
            // дата регистрации
            $post['date_reg'] = time();

            // уникальный hash
            $post['hash_string'] = crypt($post['email'] . time(), 'hashbot');

            // временный пароль для входа в crm
            $post['temp_password'] = crypt($post['email'] . time()+10, 'crmpass');

            // добавляем запись в базу
            $this->vendorRepository->add($post);

            // формируем ссылку на бота с hash 
            $linkBot = 'https://t.me/str0y_bot?start=provider_' . $post['hash_string'];
            // https://t.me/Uzstroibot?start=hazetypXJkIIk

            // возвращаем на фронт данные 
            $response = [
                'linkBot' => $linkBot,
                'login' => $post['email'],
                'tempPass' => $post['temp_password'],
            ];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        }

        protected function onDelete()
        {
            if (isset($_GET['id']))
                $this->vendorRepository->removeById($_GET);
        }

 
    }

    VendorsController::Create()->HandleRequest();
?>