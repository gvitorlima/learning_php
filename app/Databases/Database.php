<?php

namespace App\Databases;

use App\Interfaces\iDatabaseConfig;
use Exception;
use PDO;

class Database
{
  private static PDO $pdo;

  public function __construct(iDatabaseConfig $databaseInstance)
  {
    $this->configConnection($databaseInstance);
  }

  public function executeQuery(string $query)
  {
    try {

      $this->pdo->beginTransaction();
      $prepareQuery = $this->pdo->prepare($query);
      $prepareQuery->execute();

      $results = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);
      $this->pdo->commit();

      return $results;
    } catch (Exception $err) {
      $this->pdo->rollBack();
    }
  }

  private function configConnection(iDatabaseConfig $instance)
  {
    try {
      $pdo = new PDO($instance->stringConnection(), $instance->user(), $instance->password());

      $pdo->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING);
      $pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
      $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);

      $this->pdo = $pdo;
    } catch (Exception $err) {
      echo '<pre>';
      print_r($err->getMessage());
      echo '</pre>';
      exit;
    }
  }
}
