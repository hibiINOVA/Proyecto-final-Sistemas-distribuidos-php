<?php

require_once __DIR__ . '/Connection.php';

use Config\database\Connection;

$conn = Connection::connection();

if ($conn) {
    echo "Conexión exitosa a PostgreSQL";
} else {
    echo "No se pudo conectar a la base de datos";
}
