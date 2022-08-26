<?php

namespace Api\App\Core\Middlewares;

interface MiddlewareInterface
{
    public function check(array $headers): mixed;
}
