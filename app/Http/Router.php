<?php

namespace App\Http;

use Closure;

class Router
{
  private readonly string
    $prefix;
  public function __construct(string $url)
  {
    $this->setPrefix($url);
  }

  public static function GET(string $route, array $data)
  {
    self::addRoute('GET', $route, $data);
  }

  private static function addRoute(string $method, string $route, array $params)
  {
    foreach ($params as $key => $controller) {
      if ($controller instanceof Closure) {
        $params['controller'] = $controller;
        unset($params[$key]);
      }
    }

      
  }

  private function setPrefix(string $url): void
  {
    $url = parse_url($url);
    $this->prefix = $url['path'] ?? '';
  }
}
