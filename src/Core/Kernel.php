<?php

namespace Root\App\Core;

use Exception;

class Kernel
{
    public function handle(Request $request, $router = new Router): Response
    {
        try{
            $response = $router->processingRoute($request);
        }catch(Exception $e){
            $response = new Response();
            $response->setContent($e->getMessage());
        }
        return $response;
    }
    
    public function close(Response $response): never
    {
        header('Content-type: application/json');
        echo json_encode($response);

        exit;
    }
}
