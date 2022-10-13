<?php
namespace app\controller;
use app\bootstrap\views\Views;
use app\services\Telegram;

class TelegramController {

    public function actionAddContentTelegram($request) {
 
        $telegramContent = $_POST['telegram_content'] ?? '';
        $images = $_POST['images'] ?? [];
        $nameZHK = $_POST['name-zhk'] ?? '';
        $district = $_POST['district'] ?? '';
        $cookieUser = $_COOKIE['user'] ?? null;

        if(!$telegramContent) {
            $session['message']= "В Телеграм нужно, что то добавить!";
            $_SESSION['response'] = $session;
            header("Location:/");
            exit;
        }

        $telegram = new Telegram($cookieUser);
        $textTelegram = $telegram->addHashtagToContentTelegram($telegramContent,$nameZHK,$district);
        $response = $telegram->addMessageTelegramPublic(content:$textTelegram,images:$images);
        
        if(!$response->ok) {
            $session['message']= $response->description;
            $_SESSION['response'] = $session;
            header("Location:/");
            exit;
        } 

        $session['message']= "Добавлено в Телеграм!";
        $session['status'] = Views::STATUS_ALERT_SUCCESS;
        $_SESSION['response'] = $session;
        header("Location:/");
        exit;
    }
}