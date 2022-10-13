<?php
namespace app\bootstrap\middleware;
use app\bootstrap\views\Views;
use app\model\Auth;
use app\bootstrap\middleware\Middleware;

class UserMiddleware extends Middleware {

    public  function isAuthUser() {
        $cookie = $_COOKIE['user'] ?? null;
        if(!$cookie) {
            Views::getPage404(); 
            die;
        }

        $auth = new Auth();
        $user = $auth->getUserCookie($cookie);
        
        if(!$user || $user['cookie'] !== $cookie) {
            Views::getPage404(); 
            die;
        }

        return true;
    }

    public function isUser() {
        $cookie = $_COOKIE['user'] ?? null;

        if(!$cookie) return true;

        $auth = new Auth();
        $user = $auth->getUserCookie($cookie);

        if(!$user) return true;

        Views::getPage404();
        die;
    }
}