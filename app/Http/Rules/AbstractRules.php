<?php

declare(strict_types=1);

namespace app\Http\Rules;

use app\Http\Request;
use Closure;

abstract class AbstractRules
{
  public function __invoke(Request $request, Closure $next)
  {
    return $this->handle($request, $next);
  }
  abstract public function handle(Request $request, Closure $next);
}
