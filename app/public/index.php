<?php
require_once '../vendor/autoload.php';

use Route\Router;

header("Access-Control-Allow-Origin: *");
header("Content-type: application/json; charset=utf-8");
header("Access-Control-Allow-Headers: *");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

$HEADER = getallheaders();

$requestUri = rute(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$httpMethod = $_SERVER['REQUEST_METHOD'];

Rouuter::handle($requestUri, $httpMethod, $HEADER, $_GET, $_POST, file_get_contents('php://input'));

function rute( string $url)
{
    $parts = explode('/', $url);
    $publicIndex = array_search('public', $parts);

    if ($publicIndex !== false && isset($parts[$publicIndex + 1])) {
        $routeParts = array_slice($parts, $publicIndex + 1);
        return implode('/', $routeParts);
    } else {
        return '';
    }

        
}