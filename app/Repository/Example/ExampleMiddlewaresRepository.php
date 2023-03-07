<?php

namespace App\Repository\Example;

use App\Database\Database;

class ExampleMiddlewaresRepository
{
  private Database $database;
  public function __construct()
  {
    $this->database = new Database('FK_');
  }

  public function getCacheData()
  {
    $sql = "SELECT * FROM AUTH";
    $data = $this->database->executeQuery($sql);

    return $data[array_key_first($data)];
  }
}
