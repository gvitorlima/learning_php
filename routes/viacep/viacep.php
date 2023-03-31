<?php

use app\Controller\Viacep\ViacepController;
use app\Http\Middlewares\Cache;
use app\Http\Request;
use app\Http\Router;

Router::get('/cep', [
  'middlewares' => [
    Cache::class => [
      Cache::QUERY_PARAM => 'cep'
    ]
  ],
  function (Request $request) {
    return (new ViacepController)->getDataByCep($request);
  }
]);
