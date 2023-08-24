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
                if (isset($post['tg_id'])) {
                    $vendorsWithEmail = $this->vendorRepository->get([
                        'tg_id' => $post['tg_id']
                    ]);

                    if ($vendorsWithEmail != null && count($vendorsWithEmail) > 0)
                    {
                        $response = [
                            'ok' => false,
                            'error' => 'Поставщик с таким telegram id уже зарегистрирован.'
                        ];

                        echo json_encode($response);
                        return;
                    }
                }

                $response = [
                    'ok' => false,
                    'error' => 'Приглашение недействительно.'
                ];

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

                    $response = [
                        'ok' => true,
                        'payLoad' => $vendors[0]->id
                    ];
                }

                \DbContext::getConnection()->commit();

                echo json_encode($response);
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