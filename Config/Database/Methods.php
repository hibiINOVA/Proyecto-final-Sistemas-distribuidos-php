<?php

namespace Config\Database;

use Config\Database\Connection as con;

use Exception;
use Throwable;
use PDO;

class Methods
{

    public static function query(array $sql)
    {
        try {

            $db = con::connection();
            $stmt = $db->prepare($sql['query']);
            $stmt->execute($sql['params']);
            
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $results = [];
            
            while ($row = $stmt->fetchObject()) {
                $results[] = $row;
            }
        } catch (Throwable $th) {
            return ["error" => true, "msg" => "error_query", "error_code" => $th->getCode(), "details" => $th->getMessage()];
        }
        
        $db = null;
        return ["error" => false, "data" => $results]; 
    }

    public static function query_one(array $obj)
    {
        try {

            $db = con::connection();
            $stmt = $db->prepare($obj['query']);
            $stmt->execute($obj['params']);
            
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $results = $stmt->fetchObject();
            
        } catch (Throwable $th) {
            return ["error" => true, "msg" => "error_query_one", "error_code" => $th->getCode(), "details" => $th->getMessage()];
        }

        if ($results === false) $results = null;

        $db = null;
        return ["error" => false, "data" => $results]; 
    }

public static function save(array $obj)
    {
        $array = [];
        try {
            $db = con::connection();
            $stmt = $db->prepare($obj['query']); 
            $stmt->execute($obj['params']);
                        
            $db = null;
            $array = ["error" => false, "msg" => "querys_executed"];
            
        } catch (Throwable $th) {
            $db = null; 
            return ["error" => true, "msg" => "error_save", "error_code" => $th->getCode(), "details" => $th->getMessage()];
        }
        return $array;
    }
    public static function save_transaction(array $querys)
    {
        $array = [];
        try {
            $db = con::connection();
            $db->beginTransaction();
            
            foreach ($querys as $obj) {
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
            $array = ["error" => true, "msg" => "error_save_transaction", "error_code" => $th->getCode(), "details" => $th->getMessage()];
        }
        return $array;
    }
}