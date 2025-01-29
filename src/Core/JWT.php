<?php

namespace Cavesman;


use Cavesman\Config;
use Cavesman\Request;
use DomainException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use InvalidArgumentException;
use stdClass;
use UnexpectedValueException;

class JWT
{

    /**
     * @param array $data
     * @param int|null $expire
     * @return string
     */
    public static function encode(array $data, int|null $expire = null): string
    {

        $expire = $expire ?? Config::get('jwt.ttl');

        $payload = [
            'iss' => Request::getDomain(),
            'aud' => 'APP',
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + (60 * 60 * 24 * $expire)
        ];

        $payload = array_merge($payload, $data);

        return \Firebase\JWT\JWT::encode(
            $payload,
            Config::get('api.key', '{key}'),
            Config::get('api.algorithm', 'HS256')
        );
    }

    /**
     * @param string $token The JWT string
     *
     * @return stdClass The JWT's payload as a PHP object
     *
     * @throws DomainException              Provided JWT is malformed
     * @throws UnexpectedValueException     Provided JWT was invalid
     * @throws SignatureInvalidException    Provided JWT was invalid because the signature verification failed
     * @throws BeforeValidException         Provided JWT is trying to be used before it's eligible as defined by 'nbf'
     * @throws BeforeValidException         Provided JWT is trying to be used before it's been created as defined by 'iat'
     * @throws ExpiredException             Provided JWT has since expired, as defined by the 'exp' claim
     *
     * @throws InvalidArgumentException     Provided key/key-array was empty or malformed
     */
    public static function decode(string $token): stdClass
    {
        return \Firebase\JWT\JWT::decode($token, new Key(Config::get('api.key', '{key}'), Config::get('api.algorithm', 'HS256')));
    }
}
