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
        $vendor_name = '';

        if (isset($post['vendor_id']) && isset($post['text'])) {
            //достаем из БД все данные по админу (получателю)
            $admin = $this->vendorRepository->get([
                'role' => 1
            ]);
            //print_r($admin);

            //достаем из БД все данные по оптовику (отправителю)
            $vendor = $this->vendorRepository->get([
                'id' => $post['vendor_id']
            ]);
            //print_r($vendor);

            try {
                DbContext::getConnection()->beginTransaction();
                // достаем чат id админа из vendors, исходя из полученных выше данных
                $chat_id = $admin[0]->tg_id;
                //print_r($chat_id);

                // достаем имя оптовика из vendors, исходя из полученных выше данных
                $vendor_name = $vendor->name;
                //print_r($vendor_name);

                \DbContext::getConnection()->commit();
            } catch (Exception $e) {
                \DbContext::getConnection()->rollBack();
                die($e);
            }
        }

        if (!isset($post['vendor_id']) || !isset($post['text'])) {

            $response = [
                'result' => 'Отсутствует vendor_id или текст сообщения'
            ]; 
            echo $response;
            return;
        }
        
        //отправляем админу предложение оптовика через бота
        file_get_contents('https://api.telegram.org/bot'.$_ENV['BOT_TOKEN'].'/sendMessage?chat_id='. urlencode($chat_id) . 
        '&text=' . 'Новое предложение от ' . urlencode($vendor_name) . ': ' . urlencode($post['text']));
    }
}

TelegramSendLocationController::Create()->HandleRequest();
?>