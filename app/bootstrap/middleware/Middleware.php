<?php
namespace app\bootstrap\middleware;

class Middleware {


    public function runMiddleware(array $middlewareList) {
        foreach($middlewareList as $middlewareName => $action) {
            if(!$this->isExistMiddleware($middlewareName)|| !$this->isExistAction(class:$middlewareName, action:$action)) continue;

             $middleware = new $middlewareName();
             $middleware->$action();
        }
    }

    private function isExistMiddleware($middleware) {
        return file_exists(ROOT . "\\{$middleware}.php");
    }

    private function isExistAction($action, $class) {
        return method_exists($class ,$action);
    }
}