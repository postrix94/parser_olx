<?php 
namespace app\model;
use app\db\DB;
use PDO,PDOException;

class District extends DB {
    public function __construct()
    {
        parent::__construct();
    }

    public static function getAllDistrict() {
        try{
            $sql = "SELECT name_district,`value` FROM district ORDER BY `position`";
            $stmt = self::$conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }catch(PDOException $e) {
            die($e->getMessage());
        }

    }
}