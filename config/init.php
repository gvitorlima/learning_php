<?php

use App\Http\Request;
use App\Http\Response;
use App\Http\Router;

define('URL', '127.0.0.1:8080/prefix');

$routesPath = __DIR__ . '/../routes';
$routesPath = glob($routesPath . '/*/*.php');

foreach ($routesPath as $route) {
  include_once($route);
}

$objResponse = new Response(200, 'yaa');
$objRequest = new Request();

$objRouter = new Router(URL);

$objRouter->run()
  ->sendResponse();
