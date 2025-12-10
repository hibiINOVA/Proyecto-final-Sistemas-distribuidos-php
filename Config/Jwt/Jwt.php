<?php
namespace Config\Jwt;

use Config\Utils\Utils;
use DateTimeImmutable;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\Clock\FrozenClock;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\Plain;

class Jwt
{
    private static $key = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9@'; 
    private static $issuer = 'https://localhost:4200';
    private static function getConfig(): Configuration
    {
        $signer = new Sha256();
        
        $base64KeyCalculated = base64_encode(self::$key);
        
        $decodedKey = base64_decode($base64KeyCalculated); 
        
        $signingKey = InMemory::plainText($decodedKey);
        
        return Configuration::forSymmetricSigner($signer, $signingKey);
    }

    public static function SignIn($data){
        $issuedAt = new DateTimeImmutable();
        $config = self::getConfig();
        
        $builder = $config->builder()
            ->issuedBy(self::$issuer)
            ->permittedFor(sha1(Utils::get_ip())) 
            ->expiresAt($issuedAt->modify('+69 minutes'));
            
        foreach ($data as $key => $value) {
            $builder = $builder->withClaim($key, $value);
        }

        $token = $builder
            ->withClaim('iat', $issuedAt->getTimestamp())
            ->getToken($config->signer(), $config->signingKey());
            
        return $token->toString();
    }

    public static function Check(String $generated): bool
    {
        try{
            $clock = new FrozenClock(new DateTimeImmutable());
            $config = self::getConfig(); 
            
            $token = $config->parser()->parse($generated);

            if (!$token instanceof Plain) {
                 error_log("💥 JWT Parse Error: Token no es de tipo Plain."); 
                 return false;
            }

            $constraints = [
                new SignedWith($config->signer(), $config->verificationKey()), 
                new IssuedBy(self::$issuer), 
                new LooseValidAt($clock), 
            ];
            
            return $config->validator()->validate($token, ...$constraints);

        }catch(\Throwable $th){
            error_log("💥 JWT Validation Error: " . $th->getMessage()); 
            return false;
        }
    }
    public static function GetData(String $generated): ?object
    {
        try {
            $config = self::getConfig(); 
            $read = $config->parser()->parse($generated);
            
            if (!$read instanceof Plain) {
                 error_log("💥 JWT Get Data Error: Token no es de tipo Plain, no se pueden leer los claims."); 
                 return null;
            }
            
            return (object)$read->claims()->all(); 
            
        } catch (\Throwable $th) {
             error_log("💥 JWT Get Data Error: " . $th->getMessage()); 
             return null;
        }
    }
}