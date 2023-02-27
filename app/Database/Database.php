<?php

namespace App\Database;

use Exception;
use PDO;

class Database
{
  private string
    $dsn,
    $host,
    $password,
    $path,
    $stringConnection,
    $user;

  private PDO $objPdo;

  public function __construct(string $suffix = 'DB_')
  {
    $this->dsn = getenv($suffix . 'DRIVER');
    $this->host = getenv($suffix . 'HOST');
    $this->password = getenv($suffix . 'PASS');
    $this->path = getenv($suffix . 'PATH');
    $this->user = getenv($suffix . 'USER');

    $this->setStringConnection();

    $this->configConnection();
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

  private function setStringConnection()
  {
    $this->stringConnection = "$this->dsn:dbname=$this->host:$this->path;charset=utf-8;dialect=1";
  }

  private function configConnection()
  {
    try {
      $this->objPdo = new PDO($this->stringConnection, $this->user, $this->password);
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
}
