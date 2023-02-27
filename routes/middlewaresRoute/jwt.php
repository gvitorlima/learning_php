<?php

use App\Http\Middlewares\Jwt;
use App\Http\Request;
use App\Http\Router;

Router::get('/jwt', [
  'middlewares' => [
    Jwt::class
  ],
  function (Request $request) {
  }
]);
