<?php

namespace Api\App\Core\Routing\Attributes;

class BaseHttpAttribute
{
    public function __construct(
        public string $uri,
        public string $method = 'GET',
        public array $middlewares = [],
        public int $successCode = 200
    ){}
}
