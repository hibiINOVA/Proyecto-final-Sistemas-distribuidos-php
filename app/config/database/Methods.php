<?php

namespace Config\database;

use Exception;
use Throwable;
use PDO;

class Methods
{
    public static function save(Object $obj){
        $array = [];

        try
        {
            $db = con::conection();
            $stmt = $db->prepare($obj->query);
            $stmt->excute($obj->params);
            $stmt->setFetchMode(PDO::FFETCH_ASSOC);
            if($stmt->fetchColumn()){
                throw new Exception("query_error");
            }
            $db = null;
            $array = ["error" => false, "msg" => "query_excecuted"];
        }
        catch(Throwable $th)
        {
            $array = ["error" => true, "msg" => "error_save"];
        }
        return $array;
    }
}
?>