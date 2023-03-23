<?php

use App\Controller\Viacep\ViacepController;
use App\Http\Middlewares\Cache;
use App\Http\Request;
use App\Http\Router;

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
