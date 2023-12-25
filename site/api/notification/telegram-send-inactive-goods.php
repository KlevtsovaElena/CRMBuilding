<?php
include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

use abstraction\BaseController as BaseController;
use repositories\VendorRepository;

class TelegramSendLocationController extends BaseController
{
    private VendorRepository $vendorRepository;

    public function __construct()
    {
        $this->vendorRepository = new VendorRepository();
    }
    protected function onPost()
    {
        $post = json_decode(file_get_contents('php://input'), true);

        $chat_id = 0;

        if (isset($post['text'])) {
            //достаем из БД все данные по админу (получателю)
            $admin = $this->vendorRepository->get([
                'role' => 1
            ]);
            // print_r($admin);

            try {
                DbContext::getConnection()->beginTransaction();
                // достаем чат id админа из vendors, исходя из полученных выше данных
                $chat_id = $admin[0]->tg_id;
                // print_r($chat_id);

                \DbContext::getConnection()->commit();
            } catch (Exception $e) {
                \DbContext::getConnection()->rollBack();
                die($e);
            }
        }

        if (!isset($post['text'])) {

            $response = [
                'result' => 'Отсутствует текст ссылки'
            ]; 
            echo $response;
            return;
        }
        
        try{
            //отправляем админу ссылку на неактивные товары через бота
            file_get_contents('https://api.telegram.org/bot'.$_ENV['BOT_TOKEN'].'/sendMessage?chat_id='. urlencode($chat_id) . 
            '&text=' . 'Следующие товары ожидают одобрение: %0A' .  urlencode($post['text']));
            
        } catch (Exception $e) {
            echo $e;
        }
         
    }
}

TelegramSendLocationController::Create()->HandleRequest();
?>