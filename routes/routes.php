<?php
require_once './routes/Router.php';
use app\controller\{MainController,AuthController,TelegramController};
use app\bootstrap\middleware\UserMiddleware;

Router::addRouteMethodGET(['public' => MainController::class . "/index", 'middleware'=>[UserMiddleware::class => "isAuthUser",]]);
Router::addRouteMethodPOST(['public' => MainController::class .'/indexPost', 'middleware'=>[UserMiddleware::class => "isAuthUser",]]);
Router::addRouteMethodGET(['auth' => AuthController::class . '/index', 'middleware' => [UserMiddleware::class => "isUser"]]);
Router::addRouteMethodPOST(['auth' => AuthController::class . '/indexPost','middleware'=>[UserMiddleware::class => "isUser",]]);
Router::addRouteMethodPOST(['logout' => AuthController::class . '/logout']);
Router::addRouteMethodPOST(['add_telegram' => TelegramController::class . '/addContentTelegram', 'middleware'=> [UserMiddleware::class => "isAuthUser"]]);