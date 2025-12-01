<?php
namespace App\services;
use Config\database\Methods as db;

class AuthService
{
    public static function sign_up(String $user, String $password, String $phone, String $email, String $id)
    {
        $query = (object)[
            'query' => "INSERT INTO 'users' ('idUsers', 'name', 'email', 'password', 'phone') VALUES (?, ?, ?, ?, ?);",
            'params' => [$id, $user, $email, $password, $phone]
        ];
        return db::save($query);
    }
     
}