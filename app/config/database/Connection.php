<?php

namespace Config\database;

use PDO;
use PDOException;

class Connection {
    private static $host = "localhost";
    private static $db_name = "midb";
    private static $user_name = "miusuario";
    private static $password = "mipassword";
    private static $port = "5432";

    public static function connection() {
        try {
            return new PDO('pgsql:host=' . self::$host . ';port=' . self::$port . ';dbname=' . self::$db_name, self::$user_name, self::$password);
        }
        
         catch (PDOException $e) {

            return null;
        }
    }
}
