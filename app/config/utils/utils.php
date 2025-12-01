<?php
namespace Config\utils;

use Ramsey\Uuid\Uuid as uuid;

class Utils{
    public static function uuid(){
        return uudi::uuid4();
    }
    public static function hash(string $password){
        return password_hash($password,PASSWORD_DEFAULT,['cost' => 12]);
    }
    public static function verify(string $pass_plain, string $password_hash){
        return password_verify($pass_plain,$password_hash);
    }
public static function get_ip() {
    $mainIP = '';
    if (getenv('HTTP_CLIENT_IP'))
        $mainIP = getenv('HTTP_CLIENT_IP');
    else if (getenv('HTTP_X_FORWARDED_FOR'))
        $mainIP = getenv('HTTP_X_FORWARDED_FOR');
    else if (getenv('HTTP_X_FORWARDED'))
        $mainIP = getenv('HTTP_X_FORWARDED');
    else if (getenv('HTTP_FORWARDED_FOR'))
        $mainIP = getenv('HTTP_FORWARDED_FOR');
    else if (getenv('HTTP_FORWARDED'))
        $mainIP = getenv('HTTP_FORWARDED');
    else if (getenv('REMOTE_ADDR'))
        $mainIP = getenv('REMOTE_ADDR');
    else
        $mainIP = 'UNKNOWN';
    return $mainIP;
    }

}