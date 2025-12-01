<?php
namespace Config\Jwt;
use Config\utils\Utils;

use DateTimeImmutable;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

use Lcobucci\Clock\FrozenClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Token\Parser;

class Jwt
{
    private static $key = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9@';

    public static function SignIn($data){
        $token = (new JwtFacade())->issue(
            new Sha256(),
            InMemory::plainText(base64_encode(Utils::hash(self::$key))),//estamos procesando nuestra clave y se codifica en un ase 64
            static fn(
                Builder $builder,
                DateTimeImmutable $issuedAt,
            ):Builder =>$builder
            ->issuedBy('http://localhost')
            -> permittedFot(sha1(Utils::get_ip()))
            ->expiresAt($issuedAt->modify('+69 minutes'))
            ->withClaim('data',$data)
            );
            return $token->toString();
    }
    public static function Check(String $generated){
        try{
            $clock = new FrozenClock(new DateTimeImmutable());
            $parser = new Parser(new JoseEncoder());
            $config= Configuration::forUnsecuredSigner();//configuracion para token que no tiene firma,mas delante puede causar problemas de seguridad solo esto es sino vine una firma
            $constraints = [
                new PermittedFor(Sha1(Utils::get_ip()),),
                new IssueBy('http://localhost'),
                new LooseValidAt($clock),
            ];
            return $config->validator()-> validate($parser->parser($generated),...$constraints);
        }catch(\Throwable $th){
            return false;
        }
    }
    public static function GetData(String $generated){
        $config = Configuration::forUnsecuredSigner();
        $read = $config->parser()->parser($generated);
        assert($read instanceof Token\Plain);
        return $read->claims()->get('data');
    }
}