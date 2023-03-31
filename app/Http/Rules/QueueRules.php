<?php

declare(strict_types=1);

namespace app\Http\Rules;

use app\Http\Request;
use Exception;

class QueueRules
{
  public function __construct(private array $rules)
  {
  }

  public function next(Request $request)
  {
    if (empty($this->rules))
      return;
    $firstMiddleware = array_key_first($this->rules);
    $middleware = $this->rules[$firstMiddleware];

    if (!class_exists($middleware))
      throw new Exception("Regra inválida", 500);

    unset($this->rules[$firstMiddleware]);
    $actualMiddleware = new $middleware;

    $queue = $this;
    $next = function () use ($queue, $request) {
      return $queue->next($request);
    };

    return $actualMiddleware($request, $next);
  }
}
