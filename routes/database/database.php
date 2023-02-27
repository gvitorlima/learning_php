<?php

use App\Database\Database;
use App\Http\Request;
use App\Http\Router;

Router::get('/database', [
  function (Request $request) {
    return (new Database)->executeQuery('SELECT * FROM AUTH');
  }
]);
