<?php

require __DIR__.'/../vendor/autoload.php';

use Api\App\Core\Kernel;
use Api\App\Core\Request\Request;
use Dotenv\Dotenv;

// //Load env variables
$dotenv = Dotenv::createImmutable(__DIR__.'/..');
$dotenv->safeLoad();

//create a kernel instance
$kernel = new Kernel();

//handle the request
$response = $kernel->handle((new Request)->capture());

//return the response
$kernel->send($response);