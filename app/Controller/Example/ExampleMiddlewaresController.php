<?php

namespace App\Controller\Example;

use App\Database\Configs\Query;
use App\Http\Request;
use App\Http\Response;
use App\Repository\Example\ExampleMiddlewaresRepository;

class ExampleMiddlewaresController
{
  private ExampleMiddlewaresRepository $repository;
  private Response $response;

  public function __construct()
  {
    $this->repository = new ExampleMiddlewaresRepository;
    $this->response = new Response(200, '');
  }

  public function example(Request $request)
  {
    $data = $this->repository->getCacheData()[0];
    return $this->response->setResponse(200, $data);
  }
}
