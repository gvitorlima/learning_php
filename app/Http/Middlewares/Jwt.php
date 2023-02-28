<?php

namespace App\Http\Middlewares;

use App\Http\Request;
use Closure;
use Exception;

class Jwt extends AbstractMiddleware
{
  public function handle(Request $request, Closure $next)
  {
    $this->newJwt($request);
    return $next($request);
  }

  private function verifyJwt(Request $request)
  {
    try {
      $token = $request->getHeaders()['Authorization'] ?? throw new Exception("Jwt autenticação necessária", 400);
      if (!isset($token))
        throw new Exception("Verifique a autenticação passada e tente novamente", 400);

      echo '<pre>';
      print_r($_SERVER);
      echo '</pre>';
      exit;
    } catch (Exception $err) {
      echo '<pre>';
      print_r($err->getMessage());
      echo '</pre>';
      exit;
    }
  }

  public static function newJwt()
  {
    try {

      $header = [
        'alg' => 'HS256',
        'typ' => 'JWT'
      ];
    } catch (Exception $err) {
    }
  }
}
