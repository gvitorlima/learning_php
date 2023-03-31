<?php

declare(strict_types=1);

namespace app\Http;

class Response
{
  private array
    $headers;

  public function __construct(public int $code, public mixed $content)
  {
    $this->headers['Content-type'] = 'application/json';
  }

  public function setResponse(int $code, mixed $content): self
  {
    $this->code = $code;
    $this->content = $content;

    return $this;
  }

  public function addHeader(string $key, string $value)
  {
    $this->headers[$key] = $value;
  }

  public function sendResponse()
  {
    $this->mathContent();

    switch ($this->headers['Content-type']) {
      case 'application/json':

        $this->sendHeaders();
        $this->content = json_encode($this->content);
        print_r($this->content);
        exit;

      case 'text/html':

        $this->sendHeaders();
        print_r($this->content);
        exit;
    }
  }

  private function mathContent()
  {
    $match = match ($this->content) {
      is_array($this->content) => 'application/json',
      is_string($this->content) => 'text/html',

      is_a($this->content, stdClass::class) => 'application/json',

      default => 'application/json'
    };

    $this->addHeader('Content-type', $match);
  }

  private function sendHeaders()
  {
    http_response_code($this->code);
    foreach ($this->headers as $key => $value) {
      header($key . ':' . $value);
    }
  }
}
