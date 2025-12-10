<?php

namespace Config\Database;

use PDO;
use PDOException;

class Connection {
    private static ?PDO $instance = null;

    private static $host = "localhost";
    private static $db_name = "servicePhp";
    private static $user_name = "root";
    private static $password = "12345";
    private static $port = "5433";

    private function __construct() {}

    private function __clone() {}

    public static function connection(): ?PDO {
        if (self::$instance !== null) {
            return self::$instance;
        }

        try {
            $dsn = "pgsql:host=" . self::$host . ";port=" . self::$port . ";dbname=" . self::$db_name;

            self::$instance = new PDO($dsn, self::$user_name, self::$password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            return self::$instance;

        } catch (PDOException $e) {
            error_log("FATAL DB CONNECTION ERROR: " . $e->getMessage());
            return null;
        }
    }
}