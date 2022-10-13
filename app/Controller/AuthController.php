<?php
namespace app\controller;
use app\bootstrap\views\Views;
use app\model\Auth;


class AuthController {
    const MESSAGE_EMPTY_PASSWORD_LOGIN = "Неверно ввели логин или пароль";

    public function actionIndex($request) {
        return Views::getAuthPage();
    }

    public  function actionIndexPost($request) {
        if(!isset($_POST['login']) || !isset($_POST['password'])) {
            return Views::getPage404();
        }

        $login = trim($_POST['login']);
        $password = trim( $_POST['password']);

        if(empty($login) || empty($password)) {
            $session['message'] = self::MESSAGE_EMPTY_PASSWORD_LOGIN;
            $session['login'] = $login;
            $_SESSION['response'] = $session;
            header("Location:/auth");
            exit;
        }


        $auth = new Auth();
        $user = $auth->getUser($login,$password);

        if(!$user) {
            $session['message'] = self::MESSAGE_EMPTY_PASSWORD_LOGIN;
            $session['login'] = $login;
            $_SESSION['response'] = $session;
            header("Location:/auth");
            exit;
        }

        $auth->addIdUserCookie($user['login']);
        header("Location:/");
        die;
        
    }

    public function actionLogout($request) {
        Auth::logoutUser();
        header("Location: /auth");
        die;
    }


}