<?php

use app\Model\Auth;

require_once APP . "/Model/Auth.php";

$cookie = $_COOKIE['user'] ?? null;

$user = new Auth();
$cookieUser = $user->getUserCookie($cookie);

if(isset($cookieUser['cookie']) && $cookie === $cookieUser['cookie']) {
   echo '<h1><a href="/" style="z-index: 999;position:absolute;bottom:25px;right:50px;color:white;">Главная</a></h1>';
}else {
    echo '<h1><a href="/auth" style="z-index: 999;position:absolute;bottom:25px;right:50px;color:white;">Авторизация</a></h1>';
}

?>



