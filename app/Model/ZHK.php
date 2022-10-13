<?php
namespace app\model;
use app\db\DB;
use PDO,PDOException;

class ZHK extends DB {
    public function __construct()
    {
        parent::__construct();
    }

    public static function getAllZHK() {
        try{
            $sql = "SELECT name_zhk,`value` FROM zhk ORDER BY `position`";
            $stmt = self::$conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e) {
            die($e->getMessage());
        }

    }
}