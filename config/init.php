<?php

use app\Http\Request;
use app\Http\Response;
use app\Http\Router;

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
