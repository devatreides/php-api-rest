<?php

namespace Api\App\Core\Request;

use Exception;

class Request
{
    public array $query = [];

    public array $body = [];

    public array $headers = [];

    public array $cookies = [];

    public array $files = [];

    public array $server = [];

    public function capture(): Request
    {
        $this->query = $_GET;
        $this->body = $this->getBody();
        $this->headers = $this->getHeaders();
        $this->cookies = $_COOKIE;
        $this->files = $_FILES;
        $this->server = $_SERVER;

        return $this;
    }

    public function get($param): mixed
    {
        return $this->query[$param] ?? $this->body[$param] ?? throw new Exception('Param not found');
    }

    public function getAll(): array
    {
        return [...$this->query, ...$this->body];
    }

    public function getAllParams(string $expectedUri): array
    {
        $routeParams = $this->retrieveRouteParams($expectedUri);
        return [$this,...$routeParams];
    }

    private function getBody(): array
    {
        $body = !empty($_POST) ?: json_decode(file_get_contents('php://input'), true);
        
        if(json_last_error() !== JSON_ERROR_NONE){
            $body = [];
        }

        return $body;
    }

    private function getHeaders(): array
    {
        $headers = [];

        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $header = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $headers[$header] = $value;
            }
        }

        return $headers;
    }

    private function retrieveRouteParams(string $expectedUri): array
    {
        $uri = explode('/', $expectedUri);
        $requestUri = explode('/', $this->server['SCRIPT_NAME']);
        $routeParams = [];
        
        foreach ($uri as $key => $value) {
            if (str_contains($value, '{')) {
                $routeParams[str_replace('{', '', str_replace('}', '', $value))] = $requestUri[$key];
            }
        }
        
        return $routeParams;
    }
}
