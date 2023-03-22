<?php

declare(strict_types=1);

namespace App\Http\Middlewares;

use App\Http\Request;
use App\Http\Response;
use Closure;
use DateTime;
use Exception;

class Jwt extends AbstractMiddleware
{
  public function handle(Request $request, Closure $next)
  {
    $this->verifyJwt($request);
    return $next($request);
  }

  public function create(Request $request)
  {
    try {

      $payload = $request->getPayload();
      if (empty($payload))
        throw new Exception("Não foi possível criar um token, payload vazio", 500);

      $header = [
        'alg' => 'HS256',
        'typ' => 'JWT'
      ];

      $header = json_encode($header);

      $payload['created_at'] = (new DateTime())->format('Y-m-d H:i');
      $payload = json_encode($payload);

      $signature = hash_hmac("SHA256", self::base64encode($header) . '.' . self::base64encode($payload), getenv('JWT_SECRET'), true);

      return self::base64encode($header) . '.' . self::base64encode($payload) . '.' . self::base64encode($signature);
    } catch (Exception $err) {

      (new Response($err->getCode(), formatResponseError($err)))
        ->sendResponse();
    }
  }

  /**
   * Método de validação apenas fragmenta o JWT recebido em Header, Payload e na Signature. 
   * Após isso é realizado o hash dos dados com a assinatura presente no projeto, e comparada com a assinatura passada e por fim, a comparação.
   */
  public function verifyJwt(Request $request): void
  {
    try {
      $jwt = explode('Bearer ', $request->getHeaders()['Authorization']);
      if (!$jwt[1]) {
        throw new Exception("Token invalido, ou não subsequente de 'Bearer'.", 400);
      }
      $jwt = explode('.', $jwt[1]);

      $header   = $jwt[0];
      $payload  = $jwt[1];
      $token    = $this->base64decode($jwt[2]);

      if (!hash_hmac("SHA256", $header . '.' . $payload, getenv('JWT_SECRET'), true) == $token)
        throw new Exception("Não autorizado", 401);

      $payload = json_decode($this->base64decode($payload), true);

      $dateJwt = date_create($payload['created_at']);
      $dateJwt = (new DateTime)->diff($dateJwt);

      if ($dateJwt->i >= 20 || $dateJwt->h >= 1 || $dateJwt->d >= 1)
        throw new Exception("Token expirado", 401);

      return;
    } catch (Exception $err) {

      (new Response($err->getCode(), formatResponseError($err)))
        ->sendResponse();
    }
  }

  /**
   * Substitui alguns caracteres do base64 gerado.
   * 
   * Explicação:
   * 
   * Para tornar o token JWT seguro, os caracteres '+' e '/' são substituídos por outros caracteres que não são comumente usados no base64. 
   * Essa técnica é chamada de URL-safe base64 encoding. A substituição do caractere '+' pelo caractere '-' e do caractere '/' pelo caractere '_'
   * evita que esses caracteres sejam interpretados de maneira incorreta pelos navegadores da Web e outros softwares que manipulam URLs.
   * 
   * O caractere '=' é usado para preencher o final da sequência de caracteres codificados em base64 para que ela tenha um número inteiro de blocos
   * de 4 caracteres. Isso é importante porque os algoritmos de decodificação de base64 esperam que a sequência de caracteres tenha um 
   * número inteiro de blocos de 4 caracteres. No entanto, a presença do caractere = no final da sequência pode ser 
   * interpretada incorretamente pelos softwares que manipulam URLs, especialmente em ambientes web.
   * 
   * Por isso, o caractere '=' é omitido no final do token JWT, a menos que seja estritamente necessário para preencher o último bloco de caracteres.
   * Isso garante a compatibilidade do token JWT em ambientes web e evita possíveis problemas de segurança.
   */
  public function base64encode(string $data)
  {
    $data = base64_encode($data);
    return str_replace(['+', '/', '='], ['-', '_', ''], $data);
  }

  public function base64decode(string $data)
  {
    return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
  }
}
