<?php

namespace App\Http;

class Request
{
  private array
    $headers,
    $payload,
    $postVars,
    $queryParams;

  private string
    $httpMethod,
    $rootPath,
    $uri;

  public function __construct()
  {
    $this->rootPath = $_SERVER['DOCUMENT_ROOT'];
    $this->headers = getallheaders() ?? [];

    $this->uri = $_SERVER['PATH_INFO'];
    $this->httpMethod = $_SERVER['REQUEST_METHOD'];

    $this->queryParams = $_GET ?? [];
    $this->postVars = json_decode(file_get_contents('php://input'), true) ?? [];
  }

  public function setPayload(array $fields)
  {
    $this->payload = $fields;
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

  public function getPayload(?string $field = null)
  {
    if (is_null($field))
      return $this->payload;

    return $this->payload[$field];
  }

  public function getRootPath()
  {
    return $this->rootPath;
  }
}
