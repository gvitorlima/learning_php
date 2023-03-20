<?php

use App\Controller\Example\ExampleMiddlewaresController;
use App\Http\Middlewares\Cache;
use App\Http\Middlewares\Jwt;
use App\Http\Request;
use App\Http\Router;
use App\Http\Rules\Admin;

/**
 * Exemplo de uso dos middlewares em uma rota.
 */
Router::get('/example/middlewares', [
  'middlewares' => [
    Jwt::class,
    Cache::class
  ],
  'rules' => [
    Admin::class
  ],
  function (Request $request) {
    return (new ExampleMiddlewaresController)->example($request);
  }
]);
