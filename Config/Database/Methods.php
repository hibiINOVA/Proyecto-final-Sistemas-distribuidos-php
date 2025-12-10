<?php

namespace Config\Database;

use Config\Database\Connection as con;

//Excepciones específicas
use Exception;
//Excepciones en general
use Throwable;
use PDO;

class Methods
{

    // Metodo para ejecutar una consulta de varias columnas
    // AHORA ESPERA ARRAY
    public static function query(array $sql)
    {
        try {

            $db = con::connection();
            $stmt = $db->prepare($sql['query']); // Acceso con array []
            $stmt->execute($sql['params']);      // Acceso con array []
            
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $results = []; // Inicializamos el array de resultados
            
            // Si quieres que los resultados DEVUELTOS a la capa Service/Model sean objetos (stdClass)
            while ($row = $stmt->fetchObject()) {
                $results[] = $row;
            }
            // Si quieres que los resultados DEVUELTOS sean arrays asociativos (más consistente):
            // $results = $stmt->fetchAll(); 
            
        } catch (Throwable $th) {
            // Se recomienda devolver ARRAY para consistencia con el servicio
            return ["error" => true, "msg" => "error_query", "error_code" => $th->getCode(), "details" => $th->getMessage()];
        }
        
        $db = null;
        // Se recomienda devolver ARRAY para consistencia con el servicio
        return ["error" => false, "data" => $results]; 
    }

    // Metodo para ejecutar una consulta de un solo resultado    
    // AHORA ESPERA ARRAY
    public static function query_one(array $obj)
    {
        try {

            $db = con::connection();
            $stmt = $db->prepare($obj['query']); // Acceso con array []
            $stmt->execute($obj['params']);      // Acceso con array []
            
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $results = $stmt->fetchObject(); // Retorna OBJETO (stdClass)
            
        } catch (Throwable $th) {
            // Se recomienda devolver ARRAY para consistencia con el servicio
            return ["error" => true, "msg" => "error_query_one", "error_code" => $th->getCode(), "details" => $th->getMessage()];
        }

        if ($results === false) $results = null;

        $db = null;
        // Se recomienda devolver ARRAY para consistencia con el servicio, pero $results es un objeto. 
        // Si el Service espera un array: return ["error" => false, "data" => (array)$results];
        return ["error" => false, "data" => $results]; 
    }

    // Metodo para guardar los datos en la base de datos
    // AHORA ESPERA ARRAY
public static function save(array $obj)
    {
        $array = [];
        try {
            $db = con::connection();
            $stmt = $db->prepare($obj['query']); 
            $stmt->execute($obj['params']);
            
            // 🛑 ELIMINAR LA COMPROBACIÓN ERRÓNEA DE fetchColumn
            // $stmt->setFetchMode(PDO::FETCH_ASSOC);
            // if ($stmt->fetchColumn()) {
            //     throw new Exception("query_error");
            // }

            // OPCIONAL: Comprobar que al menos una fila fue afectada (no siempre necesario para INSERT)
            // if ($stmt->rowCount() === 0) {
            //     throw new Exception("no_rows_affected");
            // }
            
            $db = null;
            $array = ["error" => false, "msg" => "querys_executed"];
            
        } catch (Throwable $th) {
            // Incluimos el error completo
            $db = null; 
            return ["error" => true, "msg" => "error_save", "error_code" => $th->getCode(), "details" => $th->getMessage()];
        }
        return $array;
    }
    // Metodo que ejecuta una transacción de consultas en una base de datos
    // AHORA ESPERA ARRAY
    public static function save_transaction(array $querys)
    {
        $array = [];
        try {
            $db = con::connection();
            $db->beginTransaction();
            
            foreach ($querys as $obj) {
                // Aquí $obj es array si se lo pasaste desde el modelo como array, o objeto si lo pasaste como objeto.
                // Asumiendo que el modelo pasa array:
                $stmt = $db->prepare($obj['query']);
                $stmt->execute($obj['params']);
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
                
                $array[] = $stmt->fetchColumn();
                if (in_array(true, $array)) {
                    throw new Exception("error_in_one_of_the_queries");
                }
            }
            
            $db->commit();
            $db = null;
            $array = ["error" => false, "msg" => "querys_executed"];
        } catch (Throwable $th) {
            // Se recomienda devolver ARRAY para consistencia con el servicio
            $array = ["error" => true, "msg" => "error_save_transaction", "error_code" => $th->getCode(), "details" => $th->getMessage()];
        }
        return $array;
    }
}