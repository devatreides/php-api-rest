<?php

namespace Api\App\Core;

use Exception;
use Api\App\Core\Request\Request;
use Api\App\Core\Response\Response;
use Api\App\Core\Routing\Router;

class Kernel
{
    
    public function handle(Request $request, $router = new Router): Response
    {
        try{
            $response = $router->processingRoute($request);
        }catch(Exception $e){
            $response = new Response(['error'=>$e->getMessage()], $e->getCode() !== 0 ? $e->getCode() : 500);
        }
        return $response;
    }
    
    public function send(Response $response): never
    {
        header('Content-type: application/json');
        http_response_code($response->statusCode);
        echo json_encode($response->content, JSON_PRETTY_PRINT);

        exit;
    }
}
