<?php

namespace App\Http;

class Request
{
  private array
    $headers,
    $postVars,
    $queryParams;

  private string
    $httpMethod,
    $uri;

  public function __construct()
  {
    $this->headers = getallheaders();

    $this->uri = $_SERVER['PATH_INFO'];
    $this->httpMethod = $_SERVER['REQUEST_METHOD'];

    $this->queryParams = $_GET;
    $this->postVars = json_decode(file_get_contents('php://input'), true);
  }

  public function getPostVars()
  {
    return $this->postVars;
  }

  public function getQueryParams()
  {
    return $this->queryParams;
  }

  public function getHeaders()
  {
    return $this->headers;
  }

  public function getHttpMethod()
  {
    return $this->httpMethod;
  }

  public function getUri()
  {
    return $this->uri;
  }
}
