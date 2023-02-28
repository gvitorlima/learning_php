<?php

use App\Controller\Login\LoginController;
use App\Http\Request;
use App\Http\Router;

Router::post('/login', [
  function (Request $request) {
    return (new LoginController)->login($request);
  }
]);
