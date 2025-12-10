<?php
namespace App\Services;

use App\Models\VideojuegosModel;
use Config\Utils\Utils as util;

class VideojuegosService
{
    public static function listAll()
    {
        $res = VideojuegosModel::getAll();
        if (isset($res['error']) && $res['error']) return $res;
        return ['error' => false, 'data' => $res];
    }

    public static function getOne(string $id)
    {
        $res = VideojuegosModel::getById($id);
        
        if (isset($res['error']) && $res['error']) return $res;
        
        $results = $res['data'] ?? []; 
        
        $row = is_array($results) && count($results) ? $results[0] : null;
        
        if (!$row) return ['error' => true, 'msg' => 'Videojuego no encontrado'];
        
        return ['error' => false, 'data' => $row];
    }

    public static function create(array $payload)
    {
        if (empty($payload['titulo']) || empty($payload['usuario_registro'])) {
            return ['error' => true, 'msg' => 'titulo y usuario_registro son obligatorios'];
        }

        $id = util::uuid();

        $data = [
            'id' => $id,
            'titulo' => $payload['titulo'],
            'desarrollador' => $payload['desarrollador'] ?? null,
            'plataforma' => $payload['plataforma'] ?? null,
            'genero' => $payload['genero'] ?? null,
            'año_lanzamiento' => $payload['año_lanzamiento'] ?? null,
            'precio' => $payload['precio'] ?? null,
            'calificacion' => $payload['calificacion'] ?? null,
            'modo_juego' => $payload['modo_juego'] ?? null,
            'clasificacion' => $payload['clasificacion'] ?? null,
            'usuario_registro' => $payload['usuario_registro']
        ];

        $save = VideojuegosModel::create($data);
        if (isset($save['error']) && $save['error']) {
            return ['error' => true, 'msg' => 'Error creando videojuego'];
        }

        return ['error' => false, 'msg' => 'Videojuego creado', 'id' => $id];
    }

    public static function update(string $id, array $payload)
    {
        $exists = VideojuegosModel::getById($id);
        if (!is_array($exists) || count($exists) === 0) {
            return ['error' => true, 'msg' => 'Videojuego no encontrado'];
        }

        $up = VideojuegosModel::update($id, $payload);
        if (isset($up['error']) && $up['error']) {
            return ['error' => true, 'msg' => 'Error actualizando videojuego'];
        }

        return ['error' => false, 'msg' => 'Videojuego actualizado'];
    }

    public static function delete(string $id)
    {
        $exists = VideojuegosModel::getById($id);
        if (!is_array($exists) || count($exists) === 0) {
            return ['error' => true, 'msg' => 'Videojuego no encontrado'];
        }

        $del = VideojuegosModel::delete($id);
        if (isset($del['error']) && $del['error']) {
            return ['error' => true, 'msg' => 'Error eliminando videojuego'];
        }

        return ['error' => false, 'msg' => 'Videojuego eliminado'];
    }
}
