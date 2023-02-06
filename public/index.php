<?php

use App\Http\Request;
use App\Http\Response;
use App\Http\Router;

require_once __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ . '/../config/init.php';

$objResponse = new Response(200, 'yaa');
$objRequest = new Request();

$objRouter = new Router(URL);
