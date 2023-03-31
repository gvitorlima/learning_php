<?php

use app\Controller\Classes\JwtController;
use app\Http\Middlewares\Jwt;
use app\Http\Request;
use app\Http\Router;

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
