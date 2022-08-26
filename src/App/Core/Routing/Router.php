<?php

namespace Api\App\Core\Routing;

use Api\App\Core\Request\Request;
use Api\App\Core\Response\Response;
use Api\App\Core\Routing\Attributes\BaseHttpAttribute;
use Exception;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;

class Router
{
    public function processingRoute(Request $requestData): Response
    {
        $controllers = $this->getControllers();

        foreach ($controllers as $controller) {
            $targetRoute = $this->getTargetRoute(
                $controller->getMethods(), 
                $requestData->server
            );

            if ($targetRoute === null){
                continue;
            }

            return $this->runRoute($requestData, ...$targetRoute);
        }

        throw new Exception('Route not found', 404);
    }

    private function getControllers(): array
    {
        $controllers = array_map(function($filename) {
            $class = basename($filename, '.php');

            $class = 'Api\App\Controllers\\' . $class;

            return new ReflectionClass($class);
        }, glob(__DIR__ . '/../../Controllers/*'));

        return $controllers;
    }

    private function getTargetRoute(array $controllerMethods, array $serverData): array|null
    {
        foreach ($controllerMethods as $method) {
            $attributes = $method->getAttributes();

            foreach ($attributes as $attribute) {
                if(!$this->checkAttributePresence($attribute)){
                    continue;
                }

                $match = $this->checkRouteMatch(
                    $attribute->getArguments()['uri'], 
                    $attribute->newInstance()->method, 
                    $serverData['SCRIPT_NAME'],
                    $serverData['REQUEST_METHOD'],
                );

                if(!$match){
                    continue;
                }

                return [
                    'method' => $method,
                    'attribute' => $attribute
                ];
            }
        }

        return null;
    }

    private function runRoute(Request $request, ReflectionMethod $method, ReflectionAttribute $attribute): Response
    {
        if(isset($attribute->getArguments()['middlewares'])) {
            $request->query = [...$request->query, ...$this->checkMiddlewares($request->headers, $attribute->getArguments()['middlewares'])];
        }

        $result = $method->invoke(
            $method->getDeclaringClass()->newInstance(),
            ...$request->getAllParams($attribute->getArguments()['uri'])
        );

        return new Response($result, $attribute->getArguments()['successCode']);
    }

    private function checkMiddlewares(array $headers, array $middlewares)
    {
        $additionalInfo = [];
        foreach ($middlewares as $middleware) {
            $middleware = new $middleware;

            $additionalInfo = [...$additionalInfo, ...$middleware->check($headers)];
        }

        return array_filter($additionalInfo);
    }

    private function checkAttributePresence($attribute): bool
    {
        return is_subclass_of($attribute->getName(), BaseHttpAttribute::class);
    }

    private function checkRouteMatch($uri, $method, $scriptName, $requestMethod): bool
    {
        $uri = explode('/', $uri);
        $requestUri = explode('/', $scriptName);

        if (count($uri) !== count($requestUri)) {
            return false;
        }

        if ($method !== $requestMethod) {
            return false;
        }

        foreach ($uri as $key => $value) {
            if (str_contains($value, '{')) {
                continue;
            }
            
            if ($value !== $requestUri[$key]) {
                return false;
            }
        }

        return true;
    }
}
