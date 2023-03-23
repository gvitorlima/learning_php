<?php

declare(strict_types=1);

namespace App\Controller\Viacep;

use App\Http\Fetch;
use App\Http\Request;
use App\Http\Response;
use Exception;

class ViacepController
{
  private Response $response;
  private Fetch $fetch;

  public function __construct()
  {
    $this->response = new Response(200, '');
    $this->fetch = new Fetch;
  }

  public function getDataByCep(Request $request)
  {
    try {
      $cep = (string)$request->getQueryParams()['cep'] ??
        throw new Exception("Informe um cep", 400);

      $cep = preg_replace('/\D/', '', $cep);

      if (strlen($cep) !== 8)
        throw new Exception("Cep inválido", 400);

      $cepData = $this->fetch->get("viacep.com.br/ws/$cep/json/");
      if (isset($cepData['erro']))
        throw new Exception("Cep invalido", 400);

      $this->response->setResponse(200, $cepData);
      return $cepData;
    } catch (Exception $err) {
      echo '<pre>';
      print_r($err->getMessage());
      echo '</pre>';
      exit;
    }
  }
}
