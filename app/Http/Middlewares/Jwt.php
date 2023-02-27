<?php

namespace App\Http\Middlewares;

use App\Http\Request;
use Closure;
use Exception;

class Jwt extends AbstractMiddleware
{
  public function handle(Request $request, Closure $next)
  {
    $this->verifyJwt();
    return $next($request);
  }

  private function verifyJwt()
  {
    try {
      echo '<pre>';
      print_r('oi');
      echo '</pre>';
      exit;
    } catch (Exception $err) {
      echo '<pre>';
      print_r($err->getMessage());
      echo '</pre>';
      exit;
    }
  }
}
