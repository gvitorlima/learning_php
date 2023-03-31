<?php

declare(strict_types=1);

namespace app\Http;

use Exception;

class Fetch
{
  /**
   * CURLOPT_HTTPGET - true para redefinir o método de solicitação HTTP para GET.
   * 
   * CURLOPT_FRESH_CONNECT - true para forçar o uso de uma nova conexão em vez de uma em cache.
   * CURLOPT_HEADER - true para incluir o cabeçalho na saída.
   * 
   * CURLOPT_RETURNTRANSFER - true para retornar a transferência como uma string do valor de retorno de curl_exec() em vez de emiti-la diretamente.
   * CURLOPT_CONNECTTIMEOUT - O número de segundos a aguardar durante a tentativa de conexão. Use 0 para esperar indefinidamente.	
   * CURLOPT_DNS_CACHE_TIMEOUT - O número de segundos para manter as entradas DNS na memória.
   * CURLOPT_TIMEOUT - O número máximo de segundos para permitir que as funções cURL sejam executadas.
   * 
   * CURLOPT_HTTPHEADER - Uma matriz de campos de cabeçalho HTTP para definir, no formato array('Content-type: text/plain', 'Content-length: 100')
   * 
   * CURLOPT_POSTFIELDS - Array de informações enviadas como POST
   * 
   * CURLOPT_URL - Enviar a requisição
   * 
   * CURLINFO_HTTP_CODE - Código de resposta da requisição
   * CURLOPT_FAILONERROR - true para falhar detalhadamente se o código HTTP retornado for maior ou igual a 400.
   */

  public function get(string $url, array $headers = null)
  {
    try {
      $curl = curl_init();
      curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL => $url,
        CURLOPT_FAILONERROR => 1
      ]);

      if (!empty($headers)) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); // Uma matriz de campos de cabeçalho HTTP para definir, no formato array('Content-type: text/plain', 'Content-length: 100')
      }

      return json_decode(curl_exec($curl), true);
    } catch (Exception $err) {
    } finally {
      curl_close($curl);
    }
  }

  public function post(string $url, mixed $content, array $headers = null)
  {
    try {
      $curl = curl_init();
      curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $content,
        CURLOPT_FAILONERROR => 1
      ]);

      if (!empty($headers)) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); // Uma matriz de campos de cabeçalho HTTP para definir, no formato array('Content-type: text/plain', 'Content-length: 100')
      }

      return json_decode(curl_exec($curl), true) ?? true;
    } catch (Exception $err) {
    } finally {
      curl_close($curl);
    }
  }
}
