<?php

namespace Api\App\Core\Routing\Attributes;

use Attribute;

#[Attribute]
class Post extends BaseHttpAttribute
{
    public function __construct(
        public string $uri,
        public string $method = 'POST',
        public array $middlewares = [],
        public int $successCode = 200,
    ){}
}
