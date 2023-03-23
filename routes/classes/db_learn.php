<?php

use App\Database\LearningAuth;
use App\Http\Request;
use App\Http\Router;

Router::get('/db/learn/instance', [
  function (Request $request) {
    return print_r(LearningAuth::getInstance());
  }
]);
