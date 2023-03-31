<?php

declare(strict_types=1);

namespace app\Controller\Example;

use app\Http\Request;
use app\Http\Response;
use app\Repository\Example\ExampleMiddlewaresRepository;

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
    $this->response->setResponse(200, $data);
    return $data;
  }
}
