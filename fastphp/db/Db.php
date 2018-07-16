<?php
namespace fastphp\db;

use PDO;


class Db
{
    private static $pdo = null;
    public static function pdo($db)
    {
        if(self::$pdo !==null)
        {
            return self::$pdo;
        }
        try{
            self::setDbConfig($db);
            $dsn    = sprintf('mysql:host=%s;dbname=%s;charset=utf8', DB_HOST, DB_NAME);
            //echo $dsn;
            $option = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC);
            return self::$pdo = new PDO($dsn, DB_USER, DB_PASS, $option);
        } catch (PDOException $e) {
            exit($e->getMessage());
        }


    }

    // 配置数据库信息
    public static function setDbConfig($db)
    {
        global $config;
        define('DB_HOST', $config[$db]['host']);
        define('DB_NAME', $config[$db]['dbname']);
        define('DB_USER', $config[$db]['username']);
        define('DB_PASS', $config[$db]['password']);

    }
}

