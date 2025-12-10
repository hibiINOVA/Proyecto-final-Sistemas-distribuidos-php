<?php
namespace Config\Utils;

use Ramsey\Uuid\Uuid as uuid;

class Utils {

    public static function uuid(): string {
        return uuid::uuid4()->toString();
    }

    public static function hash(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
    }

    public static function verify(string $pass_plain, string $password_hash): bool {
        return password_verify($pass_plain, $password_hash);
    }

    public static function get_ip(): string {
        $mainIP = '';
        if (getenv('HTTP_CLIENT_IP')) {
            $mainIP = getenv('HTTP_CLIENT_IP');
        } else if (getenv('HTTP_X_FORWARDED_FOR')) {
            $mainIP = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('HTTP_X_FORWARDED')) {
            $mainIP = getenv('HTTP_X_FORWARDED');
        } else if (getenv('HTTP_FORWARDED_FOR')) {
            $mainIP = getenv('HTTP_FORWARDED_FOR');
        } else if (getenv('HTTP_FORWARDED')) {
            $mainIP = getenv('HTTP_FORWARDED');
        } else if (getenv('REMOTE_ADDR')) {
            $mainIP = getenv('REMOTE_ADDR');
        } else {
            $mainIP = 'UNKNOWN';
        }

        return $mainIP;
    }
}
