<?php

use app\Controller\Login\LoginController;
use app\Http\Request;
use app\Http\Router;

Router::post('/login', [
  function (Request $request) {
    return (new LoginController)->login($request);
  }
]);
