<?php

use App\Http\Router;
use App\Controller\Cache_\CacheController;
use App\Http\Middlewares\Cache;
use App\Http\Request;

Router::get('/cache/{print}', [
  'middlewares' => [
    Cache::class
  ],
  function (string $print, Request $request) {
    $controller = new CacheController;
    return $controller->printCache($print, $request);
  }
]);
