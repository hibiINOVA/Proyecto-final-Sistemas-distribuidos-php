<?php
namespace Router;

use Config\Jwt\Jwt;
use Config\Utils\CustomExceptions as exc;
use App\Controllers\VideojuegosController;

class Router
{
    private static $routes = [
        "GET" => [
            "videojuegos"       => [VideojuegosController::class, "index", 0],
            "videojuegos/get"   => [VideojuegosController::class, "show", 0], 
        ],

        "POST" => [
            "videojuegos/create" => [VideojuegosController::class, "create", 0],
        ],

        "PUT" => [
            "videojuegos/update" => [VideojuegosController::class, "update", 0],
        ],

        "DELETE" => [
            "videojuegos/delete" => [VideojuegosController::class, "delete", 0],
        ],

        "OPTIONS" => [
            "videojuegos/create" => ["nop", "nop", 0],
            "videojuegos/update" => ["nop", "nop", 0],
            "videojuegos/delete" => ["nop", "nop", 0],
            "videojuegos"       => ["nop", "nop", 0],
            "videojuegos/get"   => ["nop", "nop", 0],
        ]
    ];

    public static function handle(string $method, string $uri, array $HEADERS)
    {
        error_log("⚡ Router::handle() METHOD = $method URI = $uri");
        error_log("📩 HEADERS = " . json_encode($HEADERS));

        if ($method === "OPTIONS") {
            error_log("🟢 Responding OPTIONS OK");
            http_response_code(200);
            echo json_encode(["msg" => "CORS OK"]);
            return;
        }

        try {
            error_log("🔍 Checking route existence...");

            if (!isset(self::$routes[$method][$uri])) {
                error_log("❌ ROUTE NOT FOUND: $method $uri");
                throw new exc('001'); 
            }

            error_log("🟢 Route found.");

            $typeauth = self::$routes[$method][$uri][2] ?? null;

            error_log("🔐 Auth type = " . json_encode($typeauth));

            if (is_null($typeauth)) {
                error_log("❌ typeauth is null (Internal error, check route config)");
                throw new exc('001');
            }

            if ($typeauth === 0) {
                error_log("🟡 AUTH Bypass / SIMPLE required...");


                if (isset($HEADERS['simple']) && $HEADERS['simple'] !== md5('jose123')) {
                     error_log("❌ SIMPLE AUTH FAILED");
                     throw new exc('006');
                }
                
                error_log("🟢 AUTH CHECK OK (Bypassed JWT)");

            } else { 
                error_log("🔵 JWT AUTH required (TEMPORARILY DISABLED IN ROUTE CONFIG)");

                if (!isset($HEADERS['authorization']) || !Jwt::Check($HEADERS['authorization'])) {
                    error_log("❌ JWT CHECK FAILED");
                    throw new exc('006');
                }

                error_log("🟢 JWT OK");
            }

            $callback        = self::$routes[$method][$uri];
            $ControllerClass = $callback[0];
            $methodName      = $callback[1];

            error_log("🎯 Controller = $ControllerClass :: $methodName");

            if (!class_exists($ControllerClass)) {
                error_log("❌ Controller NOT FOUND");
                throw new exc('002');
            }

            $ControllerInstance = new $ControllerClass();

            if (!method_exists($ControllerInstance, $methodName)) {
                error_log("❌ Method NOT FOUND in controller");
                throw new exc('003');
            }

            error_log("📥 Getting request data...");

            $requestData = self::getRequestData($method);

            error_log("📨 RequestData = " . json_encode($requestData));

            error_log("▶ Executing controller method...");
            return call_user_func([$ControllerInstance, $methodName], $requestData);

        } catch (exc $e) {
            error_log("❗ CustomException: " . json_encode($e->GetOptions()));
            http_response_code($e->GetOptions()['status'] ?? 500);
            echo json_encode($e->GetOptions());
        } catch (\Throwable $th) {
            error_log("💥 Throwable: " . $th->getMessage());
            http_response_code(500);
            echo json_encode([
                "error"      => true,
                "msg"        => "Internal Server Error: " . $th->getMessage(),
                "error_code" => 500
            ]);
        }
    }

    private static function getRequestData(string $REQUEST_METHOD)
    {
        error_log("📦 getRequestData METHOD = $REQUEST_METHOD");

        if ($REQUEST_METHOD === 'GET' || $REQUEST_METHOD === 'DELETE') {
            $requestData = $_GET['params'] ?? null;
            
            return $requestData ? json_decode($requestData) : (object)[]; 
        } else {
            $input = file_get_contents("php://input"); 
            error_log("📨 RAW INPUT = " . $input);
            
            return $input ? json_decode($input, true) : []; 
        }
    }
}