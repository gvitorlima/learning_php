<?php

namespace App\Http\Middlewares;

use App\Http\Request;
use Closure;
use Exception;

class Queue
{
  public function __construct(private array $middle, private array $vars, private Closure $controller)
  {
  }

  public function next(Request $request)
  {
    if (empty($this->middle))
      return call_user_func_array($this->controller, $this->vars);

    foreach ($this->middle as $key => $middleware) {
      if (!class_exists($middleware))
        throw new Exception("Middleware inválido", 500);

      $actualMiddleware = new $middleware;
      unset($this->middle[$key]);

      $queue = $this;
      $next = function () use ($queue, $request) {
        return $queue->next($request);
      };

      $actualMiddleware($request, $next);
    }
  }
}
