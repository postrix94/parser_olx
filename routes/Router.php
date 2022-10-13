<?php
use app\bootstrap\views\{Views};
use app\bootstrap\middleware\Middleware;

class Router {
   private static $routes = ['GET' => [], 'POST' => []];
   private $defaultAcion = 'index';
   private $isExistUrl = false;


   public function __construct($query)
   {

    $url = self::getUri($query);
    $method = $_SERVER['REQUEST_METHOD'];
    
    foreach(self::getRoutes()[$method] as $route) {
        if(key_exists($url, $route)) {
            $this->isExistUrl = true;
            $controllerName = self::getControllerName($route[$url]);

            if(!$this->isExistController($controllerName)) {
                 Views::getPage404();
                 die;
            }

            $actionName = self::getActionName($route[$url], $method);

            if(!$this->isExistAction($actionName, $controllerName)) {
                Views::getPage404();
                die;
            }

            if(isset($route['middleware'])) {
               $middleware = new Middleware();
               $middleware->runMiddleware($route['middleware']);
            };

            $controller = new $controllerName();
            $controller->$actionName($_SERVER);
        }
    }

    if(!$this->isExistUrl) Views::getPage404();
    
   }


  public static function getRoutes() {
       return self::$routes;
   }


   public static function addRouteMethodGET($route,$method = 'GET') {
     self::$routes[$method][] = $route;
   }

   public static function addRouteMethodPOST($route,$method = 'POST') {
     self::$routes[$method][] = $route;
   }


   private function getUri($url) {
        return  trim($url, '/');
   }

   private function getControllerName($route) {
        return  explode('/', $route)[0];
        
   }

   private function isExistController($controller) {
     return file_exists(ROOT . "\\" . $controller . ".php");
   }

   private function getActionName($route,$method) {
       $action = explode('/', $route)[1] ?: $this->defaultAcion;
       return 'action' . ucfirst($action); 
   }

   private function isExistAction($action, $className) {
        return method_exists($className,$action);
   }

}