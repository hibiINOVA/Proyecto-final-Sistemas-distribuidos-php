<?php
namespace App\models;

use Config\utils\Utils as util;
use App\services\AuthService;

class AuthModel
{
    public static function sign_up(String $name, String $password, String $email, String $phone)
    {
        $id = util::uuid();
        $pass_hash = util::hash($password);
        return AuthService::sign_up($id, $name, $email, $pass_hash, $phone);
    }
}