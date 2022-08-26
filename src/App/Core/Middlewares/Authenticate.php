<?php

namespace Api\App\Core\Middlewares;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Authenticate implements MiddlewareInterface
{
    public function check(array $headers): mixed
    {
        if(!isset($headers['Authorization'])){
            throw new Exception('Unauthorized', 401);
        }

        $token = $headers['Authorization'];

        $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));

        return [
            'loggedUserEmail' => $decoded->email,
        ];
    }
}
