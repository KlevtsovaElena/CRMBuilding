<?php
include($_SERVER['DOCUMENT_ROOT'] . '/classes/autoloader.php');

use abstraction\BaseController as BaseController;

class TelegramSendLocationController extends BaseController
{
    protected function onPost()
    {
        $post = json_decode(file_get_contents('php://input'), true);

        if (!isset($post['chat_id']) || !isset($post['latitude']) || !isset($post['longitude'])) 
            return;
        
        file_get_contents('https://api.telegram.org/bot'.$_ENV['BOT_TOKEN'].'/sendlocation?chat_id='. urlencode($post['chat_id']) . '&latitude=' .
            urlencode($post['latitude']) . '&longitude=' . urlencode($post['longitude']));

            echo 1;
    }
}

TelegramSendLocationController::Create()->HandleRequest();
?>