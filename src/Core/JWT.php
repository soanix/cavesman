<?php

namespace Cavesman;


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
     * @param string|null $secret
     * @param int|null $expire
     * @return string
     */
    public static function encode(array $data, ?string $secret = null, ?int $expire = null): string
    {

        $expire = $expire ?? Config::get('jwt.ttl');

        $payload = [
            'iss' => Request::getDomain(),
            'aud' => Config::get('jwt.aud', 'App'),
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + (60 * 60 * 24 * ($expire ?? Config::get('jwt.ttl')))
        ];

        $payload = array_merge($payload, $data);

        return \Firebase\JWT\JWT::encode(
            $payload,
            $secret ?: Config::get('api.key', '{key}'),
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
    public static function decode(string $token, ?string $secret = null): stdClass
    {
        return \Firebase\JWT\JWT::decode($token, new Key($secret ?: Config::get('api.key', '{key}'), Config::get('api.algorithm', 'HS256')));
    }

    public static function parse($jwt): \Cavesman\Interface\JWT {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            throw new Exception('Token JWT inválido');
        }

        return new class(
            json_decode(base64_decode(strtr($parts[0], '-_', '+/')), true),
            json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true),
            $parts[2],
        ) implements \Cavesman\Interface\JWT {
            public function __construct(
                public array $headers,
                public array $payload,
                public string $signature,
            ) {}
        };
    }
}
