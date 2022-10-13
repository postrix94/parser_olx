<?php
namespace app\controller;
use app\bootstrap\views\Views;
use app\model\District;
use app\services\olxParser;
use app\services\Telegram;
use app\model\ZHK;

class MainController {
  
    public function actionIndex($request) {
        return  Views::getHomePage();
    }

    public function actionIndexPost($request) {
        $link = trim($_POST['link']) ?? null;
        $savePhoto = $_POST['savePhoto'] ?? null;
        $cookieUser = $_COOKIE['user'] ?? null;

        if(!$link) {
            $session['message']= "Вы не вставили ссылку!";
            $_SESSION['response'] = $session;
            header("Location:/");
            exit;
        }

        $olx = new olxParser($link,$cookieUser, $savePhoto);
        if(!$olx->validationInput()) {
           $session['message']= "Вы ввели неверное значение!";
           $_SESSION['response'] = $session;
           header("Location:/");
           exit;
        }

        $informationApartment = $olx->parsingStart();
        $telegram = new Telegram($cookieUser,$informationApartment);
        $response = $telegram->addMessageTelegramPrivate($telegram->getMessageTelegramPrivate());
        $session = [];

        if(!$response->ok) {
            $session['message']= $response->description;
            $_SESSION['response'] = $session;
            header("Location:/");
            exit;
        } 

        $session['message']= "Объект добавлен!";
        $session['link']= $telegram->getLinkTelegraph();
        $session['textTelegram']= $telegram->getTextTelegram();
        $session['imgs'] = $informationApartment['images'];
        $session['status'] = Views::STATUS_ALERT_SUCCESS;
        $session['all_zhk'] = ZHK::getAllZHK();
        $session['all_district'] = District::getAllDistrict();
        $_SESSION['response'] = $session;
        header("Location:/");
        exit;
    }

}