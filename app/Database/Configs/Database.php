<?php

namespace App\Database\Configs;

use App\Interfaces\iDatabaseConfig;
use Exception;
use PDO;

class Database
{
  private PDO $pdo;

  public function __construct(iDatabaseConfig $databaseInstance)
  {
    $this->configConnection($databaseInstance);
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
      echo '<pre>';
      print_r($err->getMessage());
      echo '</pre>';
      exit;
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
      echo '<pre>';
      print_r($err->getMessage());
      echo '</pre>';
      exit;
    }
  }
}
