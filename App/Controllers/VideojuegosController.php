<?php
namespace App\Controllers;

use Config\Utils\CustomExceptions as exc;
use App\Services\VideojuegosService;

class VideojuegosController
{
    private $service;

    public function __construct()
    {
        $this->service = new VideojuegosService();
    }

    // =======================================================================
    // CRUD IMPLEMENTACIONES
    // =======================================================================

    // GET /videojuegos
    public function index(object $requestData = null)
    {
        $res = $this->service->listAll(); 
        
        header('Content-Type: application/json');
        
        if (isset($res['error']) && $res['error']) {
            http_response_code(500);
            echo json_encode(["success" => false, "msg" => $res['msg'] ?? "Error desconocido al listar"]);
        } else {
            echo json_encode(["success" => true, "data" => $res['data'] ?? $res]);
        }
    }

    // GET /videojuegos/get?params={"id":"..."}
    public function show(object $requestData)
    {
        if (empty($requestData->id)) {
            throw new exc('005', "ID del videojuego es obligatorio."); 
        }
        
        $res = $this->service->getOne($requestData->id);
        
        header('Content-Type: application/json');

        if (isset($res['error']) && $res['error']) {
            $statusCode = ($res['msg'] === 'Videojuego no encontrado') ? 404 : 500;
            http_response_code($statusCode);
            echo json_encode(["success" => false, "msg" => $res['msg'] ?? "Error al obtener videojuego"]);
        } else {
            echo json_encode(["success" => true, "data" => $res['data'] ?? $res]);
        }
    }

    // POST /videojuegos/create
    public function create(array $requestData)
    {
        if (empty($requestData['titulo']) || empty($requestData['usuario_registro'])) {
            throw new exc('005', "El título y usuario_registro son obligatorios."); 
        }
        
        $res = $this->service->create($requestData);
        
        header('Content-Type: application/json');
        
        if (isset($res['error']) && $res['error']) {
            error_log("❌ Controller/Create FAILED. Service response: " . json_encode($res));
            
            http_response_code(400); 
            $response = ["success" => false, "msg" => $res['msg'] ?? "Error desconocido al crear"];
        } else {
            http_response_code(201);
            $response = ["success" => true, "msg" => "Videojuego creado", "data" => $res];
        }

        echo json_encode($response);
    }

    // PUT /videojuegos/update
    public function update(array $requestData)
    {
        $userId = $requestData['usuario_registro'] ?? null;

        if (empty($requestData['id']) || empty($requestData['titulo']) || empty($userId)) {
            throw new exc('005', "ID, título y usuario_registro son requeridos para actualizar.");
        }

        $res = $this->service->update($requestData['id'], $requestData);
        
        header('Content-Type: application/json');

        if (isset($res['error']) && $res['error']) {
            http_response_code(400); 
            echo json_encode(["success" => false, "msg" => $res['msg'] ?? "Error desconocido al actualizar"]);
        } else {
            http_response_code(200);
            echo json_encode(["success" => true, "msg" => "Videojuego actualizado", "data" => $res['data'] ?? $res]);
        }
    }

    // DELETE /videojuegos/delete (🛑 CORREGIDO: Acepta 'object' en lugar de 'array')
    public function delete(object $requestData) // 🛑 CAMBIO CLAVE
    {
        // Accedemos a los datos como propiedades del objeto
        $id = $requestData->id ?? null;
        // NOTA: Asumimos que $requestData->usuario_registro viene del token JWT 
        // o fue inyectado por el router, si no, se debe obtener desde el JWT.
        $userId = $requestData->usuario_registro ?? null; 
        
        // Si el userId no viene en la URL, se debe obtener del JWT. 
        // Para este ejemplo, asumimos que viene en $requestData o se ignora si el Service no lo requiere.
        
        // Usamos $id y $userId
        if (empty($id)) {
            // El usuario_registro debe ser manejado por tu capa de autenticación, 
            // pero si es requerido en el Service, lo mantenemos:
            throw new exc('005', "ID del videojuego es requerido para eliminar.");
        }

        // Llamamos al service, que realizará las comprobaciones necesarias
        $res = $this->service->delete($id, $userId); // Pasamos $id y $userId
        
        header('Content-Type: application/json');

        if (isset($res['error']) && $res['error']) {
            http_response_code(400); 
            echo json_encode(["success" => false, "msg" => $res['msg'] ?? "Error desconocido al eliminar"]);
        } else {
            http_response_code(200); // 200 OK
            echo json_encode(["success" => true, "msg" => "Videojuego eliminado", "data" => $res['data'] ?? $res]);
        }
    }
}