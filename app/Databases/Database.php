<?php

namespace App\Databases;

use Exception;
use PDO;

enum dsnPrefix
{
  case firebird;
  case mysql;
};

class Database
{
  private string
    $stringConnection;

  private static object
    $instance;

  private static dsnPrefix
    $dsn;

  private PDO $objPdo;

  public function __construct(object $instance, dsnPrefix $dsn)
  {
    self::$dsn = $dsn;
    self::$instance = $instance;

    match ($dsn) {
      dsnPrefix::firebird => $this->firebirdStringConnection($instance),
      dsnPrefix::mysql => throw new Exception("Não implementada conexão com Mysql", 500)
    };

    $this->connection();
  }

  public function executeQuery(string $query)
  {
    try {

      $this->objPdo->beginTransaction();
      $prepareQuery = $this->objPdo->prepare($query);
      $prepareQuery->execute();

      $results = $prepareQuery->fetchAll(PDO::FETCH_ASSOC);
      $this->objPdo->commit();

      return $results;
    } catch (Exception $err) {
      $this->objPdo->rollBack();
    }
  }

  private function connection()
  {
    try {
      $this->objPdo = new PDO($this->stringConnection, self::$instance::$user, self::$instance::$password);
      $this->setAttributes();
    } catch (Exception $err) {
      echo '<pre>';
      print_r($err->getMessage());
      echo '</pre>';
      exit;
    }
  }

  private function setAttributes()
  {
    $this->objPdo->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING);
    $this->objPdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
    $this->objPdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
  }

  /**
   * ! Métodos de conexão com o banco de dados FIREBIRD
   */
  private function firebirdStringConnection(object $instance)
  {
    $this->stringConnection = $instance::$dsn . ':dbname=' . $instance::$host . ':' . $instance::$path . ';charset=utf-8;dialect=1';
  }
}
