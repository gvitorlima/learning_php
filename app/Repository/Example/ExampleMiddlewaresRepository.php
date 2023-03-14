<?php

namespace App\Repository\Example;

use App\Database\Configs\Database;
use App\Database\LearningAuth;

class ExampleMiddlewaresRepository
{
  private Database $database;
  public function __construct()
  {
    $this->database = new Database(LearningAuth::getInstance());
  }

  public function getCacheData()
  {
    $sql = "SELECT * FROM AUTH";
    $data = $this->database->executeQuery($sql);

    return $data[array_key_first($data)];
  }
}
