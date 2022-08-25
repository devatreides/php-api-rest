<?php

namespace Root\App\Core;

use Exception;

class Request
{
    public $query = [];

    public $request = [];

    public $cookies = [];

    public $files = [];

    public $server = [];

    public function capture(): Request
    {
        $this->query = $_GET;
        $this->body = $this->getBody();
        $this->cookies = $_COOKIE;
        $this->files = $_FILES;
        $this->server = $_SERVER;

        return $this;
    }

    private function getBody(): array
    {
        $body = !empty($_POST) ?: json_decode(file_get_contents('php://input'), true);
        
        if(json_last_error() !== JSON_ERROR_NONE){
            $body = [];
        }

        return $body;
    }
}
