<?php

use App\Controller\Classes\JwtController;
use App\Http\Middlewares\Jwt;
use App\Http\Request;
use App\Http\Router;

Router::post('/jwt/create', [
  function (Request $request) {
    (new JwtController)->create($request);
  }
]);

Router::get('/jwt/verify', [
  'middlewares' => [
    Jwt::class
  ],
  function (Request $request) {
    return 'Token ainda válido';
  }
]);
