<?php

namespace Config\Database;

use PDO;
use PDOException;

class Connection {
    // Implementación del patrón Singleton: $instance contendrá la única conexión PDO.
    private static ?PDO $instance = null;

    // Credenciales y configuración
    private static $host = "localhost";
    private static $db_name = "servicePhp";
    private static $user_name = "root";
    private static $password = "12345";
    private static $port = "5433";

    // 1. Evita instanciación externa (constructor privado)
    private function __construct() {}

    // 2. Evita clonación
    private function __clone() {}

    /**
     * Devuelve la única instancia de la conexión PDO (PostgreSQL).
     * Nota: Mantenemos el nombre de la función original, pero con lógica Singleton.
     * @return PDO|null La conexión a la base de datos o null si falla.
     */
    public static function connection(): ?PDO {
        // Si la instancia ya existe, la devuelve
        if (self::$instance !== null) {
            return self::$instance;
        }

        // Si no existe, intenta crearla
        try {
            // DSN para PostgreSQL
            $dsn = "pgsql:host=" . self::$host . ";port=" . self::$port . ";dbname=" . self::$db_name;

            self::$instance = new PDO($dsn, self::$user_name, self::$password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,        // Lanza excepciones en caso de error SQL
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,   // Devuelve arrays asociativos por defecto
                PDO::ATTR_EMULATE_PREPARES => false,                // Mejora la seguridad contra inyección
            ]);

            return self::$instance;

        } catch (PDOException $e) {
            // CRÍTICO: Loguear el error de conexión
            error_log("❌ FATAL DB CONNECTION ERROR: " . $e->getMessage());
            return null; // Devuelve null si no puede conectar
        }
    }
}