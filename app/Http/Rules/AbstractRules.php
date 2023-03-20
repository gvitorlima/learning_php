<?php

namespace App\Http\Rules;

use App\Http\Request;
use Closure;

abstract class AbstractRules
{
  public function __invoke(Request $request, Closure $next)
  {
    return $this->handle($request, $next);
  }
  abstract public function handle(Request $request, Closure $next);
}
