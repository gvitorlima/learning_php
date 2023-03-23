<?php

declare(strict_types=1);

namespace App\Http\Middlewares;

use App\Http\Request;
use Closure;

abstract class AbstractMiddleware
{
  final public function __invoke(Request $request, Closure $next, mixed $params = null)
  {
    if ($params)
      return $this->handle($request, $next, $params);
    return $this->handle($request, $next);
  }

  abstract public function handle(Request $request, Closure $next, mixed $params = null);
}
