<?php
namespace app\model;
use app\db\DB;

use PDO;
use PDOException;

class Auth extends DB {
    // 43200 секунд === 12 часов
    private const TIME_COOKIE_USER = 43200;
    private const NAME_COOKIE_USER = 'user';

    public function __construct()
    {
        parent::__construct();
    }

    public  function getUser($login, $password) {
        try{
            $sql = "SELECT login,password FROM users WHERE login = :login AND password = :password";
            $stmt = self::$conn->prepare($sql);
            $stmt->execute([":login" => $login, ":password" => $password]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }catch(PDOException $e) {
            die($e->getMessage());
        }
    }

    public function getUserWithCookie($cookie) {

        try{
            $sql = "SELECT * FROM users WHERE cookie = :cookie";
            $stmt = self::$conn->prepare($sql);
            $stmt->execute([":cookie" => $cookie]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }catch(PDOException $e) {
            die($e->getMessage());
        }
    }

    private  function setCookieUser() {
        try{
            $idUser = uniqid(time());
            setcookie(self::NAME_COOKIE_USER, $idUser, time() + self::TIME_COOKIE_USER, '/');
            return $idUser;
        }catch(PDOException $e) {
            die($e->getMessage());
        }
    }

    public  function addIdUserCookie($login) {
        try{
            $id = $this->setCookieUser();
            $sql = "UPDATE users SET cookie=:cookie WHERE login=:login";
            $stmt = self::$conn->prepare($sql);
            $res = $stmt->execute([":cookie" => $id, ":login" => $login]);
        }catch(PDOException $e) {
            die($e->getMessage());
        }
        return $res;
    }

    public function getUserCookie($cookie) {
        try{
            $sql = "SELECT cookie from users WHERE cookie = :cookie";
            $stmt = self::$conn->prepare($sql);
            $stmt->execute([":cookie" => $cookie]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }catch(PDOException $e) {
            die($e->getMessage());
        }
    }

    public function getOlxToken($cookie) {
       try{
        $sql = "SELECT olx_tokens from users WHERE cookie = :cookie";
        $stmt = self::$conn->prepare($sql);
        $stmt->execute([':cookie'=>$cookie]);
        $tokens = $stmt->fetch(PDO::FETCH_ASSOC);

       if(isset($tokens['olx_tokens'])) {
         return $this->getRandomToken(json_decode($tokens['olx_tokens']));
       }
       return null;
       }catch(PDOException $e) {
        die($e->getMessage());
       }
    }

    
    public function deleteInvalidTokenOlx($invalidToken) {
   
    }

    private function getRandomToken($tokenList) {
        if(!is_array($tokenList)) return null;
        $position = array_rand($tokenList);
        return $tokenList[$position];
    }

    public static function logoutUser() {
        $cookie = $_COOKIE['user'] ?? null;
        if($cookie) setcookie(self::NAME_COOKIE_USER,"", time() - 60, '/');
    }
}