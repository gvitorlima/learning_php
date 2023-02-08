<?php

namespace App\Http\Middlewares;

use App\Http\Request;
use Closure;

abstract class AbstractMiddleware
{
  final public function __invoke(Request $request, Closure $next)
  {
    $this->handle($request, $next);
  }

  final static function middlewares(array $middlewares)
  {
  }

  abstract public function handle(Request $request, Closure $next);
}
