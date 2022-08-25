<?php

namespace Root\App\Core;

class Router
{
    public function processingRoute($content)
    {
        $response = new Response();
        $response->setContent($content);
        return $response;
    }

}
