<?php
namespace App\controllers;
use App\models\AuthModel;

class AuthController
{
    public static function sign_up($data)
    {
        echo json_encode(
            AuthModel::sign_up(
                $data->name,
                $data->password,
                $data->email,
                $data->phone
            )
        );
        
    }
}