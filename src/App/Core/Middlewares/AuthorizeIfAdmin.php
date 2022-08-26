<?php

namespace Api\App\Core\Middlewares;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthorizeIfAdmin
{
    public function check(array $headers): mixed
    {
        $token = $headers['Authorization'];

        $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));

        if (!$decoded->isAdmin) {
            throw new \Exception('You need to have administrative privileges to access this route.', 401);
        }

        return [];
    }
}
