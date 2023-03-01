<?php

namespace App\Http\Middlewares;

use App\Http\Request;
use Closure;
use Exception;

class Jwt extends AbstractMiddleware
{
  public function handle(Request $request, Closure $next)
  {
    $this->verifyJwt($request);
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

  public static function newJwt(Request $request)
  {
    try {

      $header = [
        'alg' => 'HS256',
        'typ' => 'JWT'
      ];

      $payload = $request->getPayload();
      if (empty($payload))
        throw new Exception("Payload vazio, impossível gerar um token", 500);

      $header   = json_encode($header);
      $payload  = json_encode($payload);

      $base64data = base64_encode($header . '.' . $payload);

      $jwt = hash_hmac('sha512', $base64data, getenv('JWT_SALT'));
      $jwt = base64_encode($header) . '.' . base64_encode($payload) . '.' . base64_encode($jwt);
      $jwt =  str_replace('=', '', $jwt);

      return [
        'token' => $jwt
      ];
    } catch (Exception $err) {
      echo '<pre>';
      print_r($err->getMessage());
      echo '</pre>';
      exit;
    }
  }
}
