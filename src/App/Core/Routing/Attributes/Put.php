<?php

namespace Api\App\Core\Routing\Attributes;

use Attribute;

#[Attribute]
class Put extends BaseHttpAttribute
{
    public function __construct(
        public string $uri,
        public string $method = 'PUT',
        public array $middlewares = [],
        public int $successCode = 200
    ){}
}
