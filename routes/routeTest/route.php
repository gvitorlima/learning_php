<?php

use App\Controller\ControllerTest\Test;
use App\Http\Router;


Router::get('/list/{id}', [
  function (int $id) {
    $controller = new Test;
    return $controller->getOpa();
  }
]);
