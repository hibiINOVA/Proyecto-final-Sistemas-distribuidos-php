<?php
// 1. CORRECCIÓN: Usar __DIR__ para una ruta absoluta y segura
require_once(__DIR__ . '/../vendor/autoload.php');

use Router\Router;

// 2. CORRECCIÓN: Implementación de cabeceras CORS
// Permitir peticiones desde Angular (localhost:4200). Usar * para desarrollo es común.
header("Access-Control-Allow-Origin: http://localhost:4200"); 
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization, simple");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Credentials: true");

// Manejo de la petición Pre-vuelo (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 3. Obtener datos de la petición
// Línea 18
$HEADER = getallheaders();

$requestUri = rute(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$httpMethod = $_SERVER['REQUEST_METHOD'];

// 4. CORRECCIÓN CRÍTICA: La variable debe ser $HEADER (singular)
// Línea 21
Router::handle($httpMethod, $requestUri, $HEADER);

/**
 * Limpia la URI para adaptarla al Router, eliminando prefijos innecesarios.
 * Asume que el Front Controller está en el directorio 'Public'.
 * @param string $url
 * @return string
 */
function rute( string $url)
{
    $parts = explode('/', $url);
    $publicIndex = array_search('Public', $parts); // Nota: Cambiado a 'Public' por si acaso

    if ($publicIndex !== false) {
        // Cortar la URL después de 'Public'
        $routeParts = array_slice($parts, $publicIndex + 1);
        $cleanRoute = implode('/', $routeParts);
        
        // Si la ruta limpia empieza/termina con '/', la eliminamos
        return trim($cleanRoute, '/');
    } 
    
    // Si no encuentra 'Public' (porque ya estamos en el router), devuelve la URI limpia
    return trim($url, '/');
}