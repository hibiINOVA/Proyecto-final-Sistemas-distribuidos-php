<?php
namespace Router;

use Config\Jwt\Jwt;
use Config\utils\CustomExceptions as exc;
use App\controllers\AuthController;

class Router
{
    private static $routes = [
        "GET" => [

        ],
        "POST" => [
            "auth/signup" => [AuthController::class, "sign_up", 0],
        ],
        "PUT" => [
        ],
        "DELETE" => [
        ],
    ];

    public static function handle(string $method, string $uri, array $HEADERS)
    {
        // http://localhost/public/signin/auth/signup/(0 o 1)
        try {
            if (!isset(self::$routes[$method][$uri])) {
                throw new exc('001'); // ruta no definida
            }

            $typeauth = self::$routes[$method][$uri][2] ?? null;
            if (is_null($typeauth)) {
                throw new exc('001');
            }

            if (!$typeauth) {
                // simple. cuando type_auth es 0
                if (!isset($HEADERS['simple']) || $HEADERS['simple'] !== md5('mike123')) {
                    throw new exc('006');
                }
            } else {
                // authorization. cuando type_auth es 1 (JWT)
                if (!isset($HEADERS['authorization']) || !Jwt::Check($HEADERS['authorization'])) {
                    throw new exc('006');
                }
            }

            $callback        = self::$routes[$method][$uri];
            $ControllerClass = $callback[0];
            $methodName      = $callback[1];

            if (!class_exists($ControllerClass)) {
                throw new exc('002');
            }

            $ControllerInstance = new $ControllerClass();

            if (!method_exists($ControllerInstance, $methodName)) {
                throw new exc('003');
            }

            $requestData = self::getRequestData($method);
            return call_user_func([$ControllerInstance, $methodName], $requestData);

        } catch (exc $e) {
            echo json_encode($e->GetOptions());
        } catch (\Throwable $th) {
            echo json_encode([
                "error"      => true,
                "msg"        => $th->getMessage(),
                "error_code" => $th->getCode()
            ]);
        }
    }

    private static function getRequestData(string $REQUEST_METHOD)
    {
        if ($REQUEST_METHOD === 'GET') {
            // si no viene params, devolvemos un objeto vacío
            $requestData = $_GET['params'] ?? null;
            return $requestData ? json_decode($requestData) : (object)[];
        } else {
            $requestData = file_get_contents("php://input"); // corregido
            return $requestData ? json_decode($requestData) : (object)[];
        }
    }
}
