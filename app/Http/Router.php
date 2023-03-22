<?php

declare(strict_types=1);

namespace App\Http;

use App\Http\Middlewares\Queue;
use App\Http\Rules\QueueRules;
use Closure;
use Exception;

class Router
{
  public static array
    $variables,
    $routes;

  private static Request
    $request;

  private static string
    $httpMethod,
    $prefix;

  private Response
    $response;

  public function __construct(string $url)
  {
    $this->setPrefix($url);

    $this->response = new Response(200, '');

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

  public function run(): Response
  {
    try {
      $route = $this->getRoute();
      if (!isset($route['controller']))
        throw new Exception('O servidor não pode processar essa requisição', 500);

      (new QueueRules(
        $route['rules'] ?? [],
      ))->next(self::$request);

      $queue = new Queue(
        $route['middlewares'] ?? [],
        $route['vars'] ?? [],
        $route['controller']
      );

      $response = $queue->next(self::$request);

      return is_a($response, Response::class)
        ? $response : $this->response->setResponse(200, $response);
    } catch (Exception $err) {
      return $this->response->setResponse(
        $err->getCode(),
        [
          'code' => $err->getCode(),
          'message' => $err->getMessage()
        ]
      );
    }
  }

  public function getRoute()
  {
    $uri = self::getUri();

    foreach (self::$routes as $patternRoute => $method) {

      if (preg_match($patternRoute, $uri, $matches)) {

        if (isset($method[self::$httpMethod])) {
          unset($matches[0]);

          if (key_exists('vars', $method[self::$httpMethod])) {
            $vars = $method[self::$httpMethod]['vars'][0];
            $method[self::$httpMethod]['vars'] = array_combine(
              $vars,
              $matches
            );
          }

          $method[self::$httpMethod]['vars']['request'] = self::$request;
          return $method[self::$httpMethod];
        }

        throw new Exception("Método não permitido", 405);
      }
    }

    throw new Exception("Rota não encontrada", 404);
  }

  private static function addRoute(string $method, string $route, array $params)
  {
    foreach ($params as $key => $controller) {
      if ($controller instanceof Closure) {
        $params['controller'] = $controller;
        unset($params[$key]);
      }
    }

    self::getVariables($route, $params);

    $patternRoute = '/^' . str_replace('/', '\/', $route) . '$/';
    self::$routes[$patternRoute][$method] = $params;
  }

  private static function getVariables(string &$route, array &$params)
  {
    $patternVariables = '/{(.*?)}/';

    if (preg_match_all($patternVariables, $route, $matches)) {
      unset($matches[0]);
      $params['vars'] = array_values($matches);

      $route = preg_replace($patternVariables, '(.*?)', $route);
    }
  }

  private static function getUri()
  {
    $uri = self::$request->getUri();
    $uri = str_replace(self::$prefix, '', $uri) ?? $uri;

    if (str_ends_with($uri, '/')) {
      $uri = substr($uri, 0, strlen($uri) - 1);
    }
    return $uri;
  }

  private function setPrefix(string $url): void
  {
    $url = parse_url($url);
    self::$prefix = $url['path'] ?? '';
  }
}
