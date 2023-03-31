<?php

use app\Controller\Example\ExampleMiddlewaresController;
use app\Http\Middlewares\Cache;
use app\Http\Middlewares\Jwt;
use app\Http\Request;
use app\Http\Router;
use app\Http\Rules\Admin;

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
