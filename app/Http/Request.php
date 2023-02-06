<?php

namespace App\Http;

class Request
{
  private array
    $postVars,
    $queryParams,
    $headers;

  private string
    $httpMethod,
    $uri;

  public function __construct()
  {
    $this->headers = getallheaders();

    $this->uri = $_SERVER['PATH_INFO'];
    $this->httpMethod = $_SERVER['REQUEST_METHOD'];

    $this->postVars = $_POST;
    $this->queryParams = $_GET;
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
