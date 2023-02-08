<?php

namespace App\Http\Middlewares;

use App\Http\Request;
use Closure;

class Cache extends AbstractMiddleware
{
  public function cache(Closure $next)
  {
    $response = call_user_func($next);
    return $response;
  }
  public function handle(Request $request, Closure $next)
  {
    return $this->cache($next);
  }
}
