<?php
include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

use abstraction\BaseController as BaseController;
use repositories\CustomerRepository;
use repositories\SettingsRepository;

class TelegramSendLocationController extends BaseController
{
    private CustomerRepository $customerRepository;
    private SettingsRepository $settingsRepository;

    public function __construct()
    {
        $this->customerRepository = new CustomerRepository();
        $this->settingsRepository = new SettingsRepository();
    }
    protected function onGet()
    {
        
        $chat_id = 0;

        if (isset($_GET['customer_id']) && $_GET['customer_id']) {

            //достаем из БД все данные по клиенту (получателю)
            $customer = $this->customerRepository->get([
                'id' => $_GET['customer_id']
            ]);
            //print_r($customer);

            try {
                DbContext::getConnection()->beginTransaction();
                // достаем чат id клиента из customers, исходя из полученных выше данных
                $chat_id = $customer->tg_id;
                //print_r($chat_id);

                \DbContext::getConnection()->commit();
            } catch (Exception $e) {
                \DbContext::getConnection()->rollBack();
                die($e);
            }

            //достаем из БД телефон админа для связи
            $adminContacts = $this->settingsRepository->get([
                'value'
            ]);

            $adminContact =  $adminContacts[0]->value;
            //print_r($adminContact);

            //и first_name (обязательно для пересылки телефона в виде контакта через бота)
            $adminName =  $adminContacts[0]->first_name;
            //print_r($adminName);

            //высылаем через бота контакт админа
            file_get_contents('https://api.telegram.org/bot'.$_ENV['BOT_TOKEN'].'/sendContact?chat_id='. urlencode($chat_id) . 
            '&phone_number='.  urlencode($adminContact) . '&first_name=' . urlencode($adminName));
        }

        if (!isset($_GET['customer_id'])) {

            $response = 'В запросе отсутствует customer_id';
            echo $response;
            return;
        }
        
    }
}

TelegramSendLocationController::Create()->HandleRequest();
?>