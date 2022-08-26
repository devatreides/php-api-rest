<?php

namespace Api\App\Core\Response;

class Response
{
    public function __construct(
        public readonly mixed $content,
        public readonly int $statusCode = 200
    ) {}
}
