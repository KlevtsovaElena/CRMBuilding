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

        $db = \DbContext::getConnection();

        // если передали id то редактирование по id
        if (isset($post['id'])) {

            $this->vendorRepository->updateById($post);
            if (isset($post['step']) && isset($post['id'])) {
                $db->query("UPDATE vendors SET step = " . (int)$post['step'] ." WHERE id = " . (int)$post['id'] );
            }
            return;
        }

        // привязка нового вендора по hash (tg_id и очистка хэша)
        if (isset($post['hash_string'])) {

            $sql = "UPDATE vendors SET
                                               tg_id = " . $post['tg_id'] . ",
                                               tg_username = '" . $post['tg_username'] . "',
                                               step = 2
                                           WHERE hash_string = '" . $post['hash_string'] . "'";

            $db->query($sql);

            $response = [
                'ok' => true,
                'payload' => $db->lastInsertId()
            ];

            echo json_encode($response);
            return;
        }

        if (!isset($post['email'])) {
            $response = [
                'error' => 'Необходимо заполнить поле email.'
            ];

            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            return;
        }

        $existsVendor = $this->vendorRepository->get([
            'email' => $post['email']
        ]);

        if ($existsVendor != null && count($existsVendor) > 0) {
            $response = [
                'error' => 'Поставщик с таким email уже существует.'
            ];

            echo json_encode($response);
            return;
        }

        // добавим недостающие поля
        // дата регистрации
        $post['date_reg'] = time();

        $timeToken = time();
        $timeStr = (string) $timeToken;
        $timeStr = base_convert($timeStr, 10, 16);

        // уникальный hash
        $hash_string =  crypt($timeStr . $post['email'], 'hashbot'); 
        $post['hash_string'] = preg_replace("/[^a-zA-Z0-9]/", "", $hash_string);

        // пароль для входа в crm
        $password = crypt($timeStr . $post['email'] . time() + 10, 'crmpass');
        $post['password'] = preg_replace("/[^a-zA-Z0-9]/", "", $password);

        // добавляем запись в базу
        $this->vendorRepository->add($post);

        // формируем ссылку на бота с hash 
        // для сервера
        $linkBot = 'https://t.me/str0y_bot?start=provider_' . $post['hash_string'];

        // для локали
        // $linkBot = 'https://t.me/Uzstroibot?start=provider_' . $post['hash_string'];       

        // возвращаем на фронт данные 
        $response = [
            'linkBot' => $linkBot,
            'login' => $post['email'],
            'pass' => $post['password'],
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