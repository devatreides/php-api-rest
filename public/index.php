<?php

require __DIR__.'/../vendor/autoload.php';

use Root\App\Core\Kernel;
use Root\App\Core\Request;

//create a kernel instance
$kernel = new Kernel();

//handle the request
$response = $kernel->handle((new Request)->capture());

//return the response and terminate the application
$kernel->close($response);