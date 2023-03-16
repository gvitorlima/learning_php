<?php

namespace App\Database\Configs;

use App\Http\Response;
use App\Interfaces\iDatabaseConfig;
use Exception;
use PDO;

class Database
{
  private PDO $pdo;
  private Response $response;

  public function __construct(iDatabaseConfig $databaseInstance)
  {
    $this->configConnection($databaseInstance);
    $this->response = new Response(200, '');
  }

  public function executeQuery(array $queryAndValues)
  {
    try {
      $this->pdo->beginTransaction();

      $prepareQuery = $this->pdo->prepare($queryAndValues['QUERY']);
      $prepareQuery->execute($queryAndValues['VALUES']);

      $results = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);

      $this->pdo->commit();

      return $results;
    } catch (Exception $err) {
      $this->pdo->rollBack();
      $err = $this->response->setResponse($err->getCode(), formatResponseError($err));
      return $this->response->sendResponse();
    }
  }

  private function configConnection(iDatabaseConfig $instance)
  {
    try {

      $this->pdo = new PDO($instance->stringConnection(), $instance->user(), $instance->password());

      $this->pdo->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING);
      $this->pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
      $this->pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
    } catch (Exception $err) {
      $err = $this->response->setResponse($err->getCode(), formatResponseError($err));
      return $this->response->sendResponse();
    }
  }
}
