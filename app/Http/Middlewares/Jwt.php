<?php

namespace App\Http\Middlewares;

use App\Http\Request;
use Closure;
use Exception;

class Jwt extends AbstractMiddleware
{
  public function handle(Request $request, Closure $next)
  {
    // $this->verifyJwt($request);
    return $next($request);
  }

  public static function create(Request $request)
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
      $payload = json_encode($payload);

      $signature = hash_hmac("SHA256", self::base64encode($header) . '.' . self::base64encode($payload), getenv('JWT_SALT'), true);

      $jwt = self::base64encode($header) . '.' . self::base64encode($payload) . '.' . self::base64encode($signature);
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

  private function verifyJwt(Request $request)
  {
    try {
    } catch (Exception $err) {
      echo '<pre>';
      print_r($err->getMessage());
      echo '</pre>';
      exit;
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
  private static function base64encode(string $data)
  {
    $data = base64_encode($data);
    return str_replace(['+', '/', '='], ['-', '_', ''], $data);
  }

  private function base64decode(string $data)
  {
    return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
  }
}
