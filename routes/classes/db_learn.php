<?php

use app\Database\LearningAuth;
use app\Http\Request;
use app\Http\Router;

Router::get('/db/learn/instance', [
  function (Request $request) {
    return print_r(LearningAuth::getInstance());
  }
]);
