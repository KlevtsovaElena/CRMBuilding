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
        if (isset($post['id'])) {
            $this->vendorRepository->updateById($post);
            return;
        }

        // редактирование по hash (tg_id и очистка хэша)
        if (isset($post['hash_string'])) {
            try {
                DbContext::getConnection()->beginTransaction();
                $this->vendorRepository->updateByHash($post);
                $vendors = $this->vendorRepository->get([
                    'hash_string' => $post['hash_string']
                ]);

                if ($vendors != null && count($vendors) > 0) {
                    $this->vendorRepository->updateById([
                        'id' => $vendors[0]->id,
                        'hash_string' => null
                    ]);
                }

                \DbContext::getConnection()->commit();
                return;
            } catch (Exception $e) {
                \DbContext::getConnection()->rollBack();
                die($e);
            }
        }

        if (!isset($post['email'])) {
            $response = [
                'error' => 'Необходимо заполнить поле email.'
            ];

            echo json_encode($response);
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
        $post['hash_string'] = crypt($timeStr . $post['email'], 'hashbot');

        // пароль для входа в crm
        $post['password'] = crypt($timeStr . $post['email'] . time() + 10, 'crmpass');

        // добавляем запись в базу
        $this->vendorRepository->add($post);

        // формируем ссылку на бота с hash 
        $linkBot = 'https://t.me/str0y_bot?start=provider_' . $post['hash_string'];
        // https://t.me/Uzstroibot?start=hazetypXJkIIk

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