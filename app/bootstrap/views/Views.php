<?php
namespace app\bootstrap\views;

class Views {
    private const TITLE_PAGE_404 = 'Страница не найдена';
    private const TITLE_HOME_PAGE = 'Главная';
    private const TITLE_AUTH_PAGE = 'Авторизация';
    private const STATUS_ALERT_DANGER = 'danger';
    const STATUS_ALERT_SUCCESS = 'success';


    public static function getPage404($title = self::TITLE_PAGE_404) {
        http_response_code(404);
        return include_once ROOT . '/app/views/pages/404.php';
         
    }

    public static function getHomePage($message = null,$title = self::TITLE_HOME_PAGE,$status= self::STATUS_ALERT_DANGER) {
        return require_once ROOT . '/app/views/pages/home.php';
    }

    public static function getAuthPage($title = self::TITLE_AUTH_PAGE, $message = null, $login = null) {
        return require_once ROOT . '/app/views/pages/auth.php';
    }

    public static function getAlert($status = self::STATUS_ALERT_DANGER,$message = null) {
        return require_once ROOT . '/app/views/partials/alert.php';
    }

 
}