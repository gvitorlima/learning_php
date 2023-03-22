<?php

declare(strict_types=1);

namespace App\Http\Rules;

use App\Http\Middlewares\Jwt;
use App\Http\Request;
use App\Http\Response;
use Closure;
use Exception;

class Admin extends AbstractRules
{
  private static string $permission = 'admin';
  private Response $response;

  public function __construct()
  {
    $this->response = new Response(200, '');
  }


  public function handle(Request $request, Closure $next)
  {
    $this->verify($request);
    return $next($request, $next);
  }

  private function verify(Request $request)
  {
    try {
      $objJwtClass = new Jwt;
      $objJwtClass->verifyJwt($request);

      $token = explode('Bearer ', $request->getHeaders()['Authorization']);
      $token = explode('.', $token[1]);

      $payload  = $token[1];

      $dataUser = json_decode($objJwtClass->base64decode($payload), true);
      if ($dataUser['permission'] !== self::$permission || !isset($dataUser['permission']))
        throw new Exception("Rota não permitida.", 401);

      return;
    } catch (Exception $err) {
      $this->response->setResponse($err->getCode(), formatResponseError($err));
      return $this->response->sendResponse();
    }
  }
}
