<?php

namespace App\Http\Middlewares;

use App\Http\Request;
use Closure;
use Exception;

class Queue
{
  public function __construct(private array $middlewares, private array $vars, private Closure $controller)
  {
  }

  public function next(Request $request)
  {
    if (empty($this->middlewares))
      return call_user_func_array($this->controller, $this->vars);

    $firstMiddleware = array_key_first($this->middlewares);
    $middleware = $this->middlewares[$firstMiddleware];
    
    if (!class_exists($middleware))
      throw new Exception("middlewares inválido", 500);

    unset($this->middlewares[$firstMiddleware]);
    $actualMiddleware = new $middleware;

    $queue = $this;
    $next = function () use ($queue, $request) {
      return $queue->next($request);
    };

    return $actualMiddleware($request, $next);
  }
}
