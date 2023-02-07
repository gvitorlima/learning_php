<?php

namespace App\Http;

use Closure;
use Exception;

class Router
{
  public static array
    $routes;

  private static Request
    $request;

  private static string
    $httpMethod,
    $prefix;

  public function __construct(string $url)
  {
    $this->setPrefix($url);

    self::$request = new Request;
    self::$httpMethod = self::$request->getHttpMethod();
  }

  public static function get(string $route, array $data)
  {
    self::addRoute('GET', $route, $data);
  }

  public static function post(string $route, array $data)
  {
    self::addRoute('POST', $route, $data);
  }

  public static function put(string $route, array $data)
  {
    self::addRoute('PUT', $route, $data);
  }

  public static function delete(string $route, array $data)
  {
    self::addRoute('DELETE', $route, $data);
  }

  public function run()
  {
    try {
      $route = $this->getRoute();
      if (!isset($route['controller']))
        throw new Exception('O servidor não pode processar essa requisição', 500);

      echo call_user_func_array($route['controller'], $route['vars']);
      exit;
    } catch (Exception $err) {
    }
  }

  public function getRoute()
  {
    $uri = self::getUri();

    foreach (self::$routes as $patternRoute => $method) {
      if (preg_match($patternRoute, $uri, $matches))
        if ($method[self::$httpMethod]) {
          unset($matches[0]);

          $method[self::$httpMethod]['vars'] = array_combine(
            $method[self::$httpMethod]['vars'][0],
            $matches
          );
          return $method[self::$httpMethod];
        }

      throw new Exception("Método não permitido", 405);
    }

    throw new Exception("Rota não encontrada", 404);
  }

  private static function addRoute(string $method, string $route, array $params, $group = null)
  {
    foreach ($params as $key => $controller) {
      if ($controller instanceof Closure) {
        $params['controller'] = $controller;
        unset($params[$key]);
      }
    }

    $patternVariables = '/{(.*?)}/';
    if (preg_match_all($patternVariables, $route, $matches)) {
      unset($matches[0]);
      $params['vars'] = array_values($matches);

      $route = preg_replace($patternVariables, '(.*?)', $route);
    }

    $patternRoute = '/^' . str_replace('/', '\/', $route) . '$/';
    self::$routes[$patternRoute][$method] = $params;
  }

  private function setPrefix(string $url): void
  {
    $url = parse_url($url);
    self::$prefix = $url['path'] ?? '';
  }

  public static function getUri()
  {
    $uri = self::$request->getUri();
    $uri = str_replace(self::$prefix, '', $uri) ?? $uri;

    if (str_ends_with($uri, '/')) {
      $uri = substr($uri, 0, strlen($uri) - 1);
    }
    return $uri;
  }
}
