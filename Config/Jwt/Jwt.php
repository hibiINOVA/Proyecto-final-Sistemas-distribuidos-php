<?php
namespace Config\Jwt;

use Config\Utils\Utils;
use DateTimeImmutable;

// Importaciones necesarias de Lcobucci\JWT
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\Clock\FrozenClock;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\Plain; // Necesario para acceder a claims()

class Jwt
{
    // CLAVE SINCRONIZADA con el 'key' de Node.js
    private static $key = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9@'; 
    private static $issuer = 'https://localhost:4200'; // Sincronizado con Node.js

    // =======================================================================
    // FUNCIÓN DE AYUDA (Para no repetir la configuración y sincronizar la clave)
    // Se usa la lógica de decodificación inversa que ha sido necesaria para sincronizar
    // las librerías JWT de Node.js y PHP con esta clave particular.
    // =======================================================================
    private static function getConfig(): Configuration
    {
        $signer = new Sha256();
        
        // 1. Obtener la clave Base64 calculada (como lo haría Node.js)
        $base64KeyCalculated = base64_encode(self::$key);
        
        // 2. Usar la clave DECODIFICADA (Texto Plano) para la verificación HMAC.
        // Esto compensa el comportamiento interno de 'jsonwebtoken' vs. 'lcobucci/jwt'.
        $decodedKey = base64_decode($base64KeyCalculated); 
        
        $signingKey = InMemory::plainText($decodedKey);
        
        // El método forSymmetricSigner es crucial para tokens firmados (HS256)
        return Configuration::forSymmetricSigner($signer, $signingKey);
    }

    // =======================================================================
    // 1. CREACIÓN DEL TOKEN (SignIn)
    // Adapta el payload a la estructura plana que usa Node.js (sin la clave 'data')
    // =======================================================================
    public static function SignIn($data){
        $issuedAt = new DateTimeImmutable();
        $config = self::getConfig();
        
        $builder = $config->builder()
            ->issuedBy(self::$issuer)
            ->permittedFor(sha1(Utils::get_ip())) 
            ->expiresAt($issuedAt->modify('+69 minutes'));
            
        // CRÍTICO: Añadir cada dato individualmente al builder (ID, nombre, etc.)
        foreach ($data as $key => $value) {
            $builder = $builder->withClaim($key, $value);
        }

        $token = $builder
            ->withClaim('iat', $issuedAt->getTimestamp())
            ->getToken($config->signer(), $config->signingKey());
            
        return $token->toString();
    }

    // =======================================================================
    // 2. VERIFICACIÓN DEL TOKEN (Check)
    // =======================================================================
    public static function Check(String $generated): bool
    {
        try{
            $clock = new FrozenClock(new DateTimeImmutable());
            $config = self::getConfig(); 
            
            $token = $config->parser()->parse($generated);

            // Asegurarse de que el token es de tipo Plain (no cifrado)
            if (!$token instanceof Plain) {
                 error_log("💥 JWT Parse Error: Token no es de tipo Plain."); 
                 return false;
            }

            $constraints = [
                // 1. Verificar la firma (SignedWith)
                new SignedWith($config->signer(), $config->verificationKey()), 
                // 2. Verificar el emisor (IssuedBy)
                new IssuedBy(self::$issuer), 
                // 3. Verificar la expiración (LooseValidAt)
                new LooseValidAt($clock), 
            ];
            
            return $config->validator()->validate($token, ...$constraints);

        }catch(\Throwable $th){
            // Registra errores de firma, expiración o formato
            error_log("💥 JWT Validation Error: " . $th->getMessage()); 
            return false;
        }
    }

    // =======================================================================
    // 3. OBTENER DATOS (GetData)
    // Devuelve el payload completo como objeto (id, nombre, email, etc.)
    // =======================================================================
    public static function GetData(String $generated): ?object
    {
        try {
            $config = self::getConfig(); 
            $read = $config->parser()->parse($generated);
            
            if (!$read instanceof Plain) {
                 error_log("💥 JWT Get Data Error: Token no es de tipo Plain, no se pueden leer los claims."); 
                 return null;
            }
            
            // Devolvemos todos los claims, ya que el ID y los datos están en el nivel superior.
            return (object)$read->claims()->all(); 
            
        } catch (\Throwable $th) {
             error_log("💥 JWT Get Data Error: " . $th->getMessage()); 
             return null;
        }
    }
}