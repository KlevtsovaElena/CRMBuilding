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
            //print_r($admin);

            try {
                DbContext::getConnection()->beginTransaction();
                // достаем чат id админа из vendors, исходя из полученных выше данных
                $chat_id = $admin[0]->tg_id;
                //print_r($chat_id);

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

        // $reply = "Следующие товары больше не активны:";


        // $keyboard = array(
        //     "inline_keyboard" => array(array(array("text" => "X", "url" => urlencode($post['text']))))
        // );

        // $keyboard = json_encode($keyboard, true);

        // $sendto = "https://api.telegram.org/bot" . $_ENV['BOT_TOKEN'] . "/sendMessage?chat_id=". urlencode($chat_id) ."&text=". $reply ."&parse_mode=HTML&reply_markup=" .$keyboard;

        // file_get_contents($sendto);

        file_get_contents('https://api.telegram.org/bot'.$_ENV['BOT_TOKEN'].'/sendMessage?chat_id='.$chat_id.'&text=Следующие товары больше не активны: &reply_markup={"inline_keyboard":[[{"text":"Press here to open URL","url": ' . urlencode($post['text']) . '}]]}');
        
        //отправляем админу предложение оптовика через бота
        // file_get_contents('https://api.telegram.org/bot'.$_ENV['BOT_TOKEN'].'/sendMessage?chat_id='. urlencode($chat_id) . 
        // '&text=' . 'Следующие товары больше не активны: ' . '&parse_mode=HTML&text=' . urlencode($post['text']));
    }
}

TelegramSendLocationController::Create()->HandleRequest();
?>