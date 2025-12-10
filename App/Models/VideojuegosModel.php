<?php
namespace App\Models;

use Config\Database\Methods as db;

class VideojuegosModel
{
    // Listar todos (devuelve array)
    public static function getAll()
    {
        // CORREGIDO: Usar ARRAY [] en lugar de OBJETO (object)[]
        $query = [
            'query' => "SELECT id, titulo, desarrollador, plataforma, genero, año_lanzamiento, precio, calificacion, modo_juego, clasificacion, usuario_registro, fecha_creacion, fecha_actualizacion
                        FROM videojuegos
                        ORDER BY fecha_creacion DESC",
            'params' => []
        ];

        return db::query($query);
    }

    // Obtener uno por id
    public static function getById(string $id)
    {
        // CORREGIDO: Usar ARRAY [] en lugar de OBJETO (object)[]
        $query = [
            'query' => "SELECT id, titulo, desarrollador, plataforma, genero, año_lanzamiento, precio, calificacion, modo_juego, clasificacion, usuario_registro, fecha_creacion, fecha_actualizacion
                        FROM videojuegos
                        WHERE id = ? LIMIT 1",
            'params' => [$id]
        ];

        return db::query($query);
    }

    // Crear nuevo videojuego
    public static function create(array $data)
    {
        // CORREGIDO: Usar ARRAY [] en lugar de OBJETO (object)[]
        $query = [
            'query' => "INSERT INTO videojuegos
                        (id, titulo, desarrollador, plataforma, genero, año_lanzamiento, precio, calificacion, modo_juego, clasificacion, usuario_registro, fecha_creacion, fecha_actualizacion)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)",
            'params' => [
                $data['id'],
                $data['titulo'],
                $data['desarrollador'] ?? null,
                $data['plataforma'] ?? null,
                $data['genero'] ?? null,
                $data['año_lanzamiento'] ?? null,
                $data['precio'] ?? null,
                $data['calificacion'] ?? null,
                $data['modo_juego'] ?? null,
                $data['clasificacion'] ?? null,
                $data['usuario_registro']
            ]
        ];

        return db::save($query);
    }

    // Actualizar (campos dinámicos)
    public static function update(string $id, array $data)
    {
        $fields = [];
        $params = [];

        $allowed = ['titulo','desarrollador','plataforma','genero','año_lanzamiento','precio','calificacion','modo_juego','clasificacion','usuario_registro'];

        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }

        if (count($fields) === 0) {
            return ['error' => true, 'msg' => 'No hay campos para actualizar'];
        }

        $params[] = $id;

        // CORREGIDO: Usar ARRAY [] en lugar de OBJETO (object)[]
        $query = [
            'query' => "UPDATE videojuegos
                        SET " . implode(', ', $fields) . ", fecha_actualizacion = CURRENT_TIMESTAMP
                        WHERE id = ?",
            'params' => $params
        ];

        return db::save($query);
    }

    // Eliminar
    public static function delete(string $id)
    {
        // CORREGIDO: Usar ARRAY [] en lugar de OBJETO (object)[]
        $query = [
            'query' => "DELETE FROM videojuegos WHERE id = ?",
            'params' => [$id]
        ];

        return db::save($query);
    }
}