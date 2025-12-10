<?php
require_once(__DIR__ . '/../vendor/autoload.php');

use Router\Router;

header("Access-Control-Allow-Origin: http://localhost:4200"); 
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization, simple");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$HEADER = getallheaders();

$requestUri = rute(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$httpMethod = $_SERVER['REQUEST_METHOD'];

Router::handle($httpMethod, $requestUri, $HEADER);

function rute( string $url)
{
    $parts = explode('/', $url);
    $publicIndex = array_search('Public', $parts);
    if ($publicIndex !== false) {
        $routeParts = array_slice($parts, $publicIndex + 1);
        $cleanRoute = implode('/', $routeParts);
        
        return trim($cleanRoute, '/');
    } 
    
    return trim($url, '/');
}