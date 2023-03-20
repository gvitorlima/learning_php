<?php

namespace App\Repository\Login;

use App\Database\Configs\Database;
use App\Database\Configs\Query;
use App\Database\LearningAuth;

class LoginRepository
{
  private Database $database;
  private Query $queryBuilder;
  public function __construct()
  {
    $this->database = new Database(LearningAuth::getInstance());
    $this->queryBuilder = Query::getInstance();
  }

  public function get(string $email)
  {
    $query = $this->queryBuilder->select('*', 'AUTH a')
      ->where('a.EMAIL', $email)->getQuery();

    $data = $this->database->executeQuery($query);
    return $data[array_key_first($data)] ?? [];
  }
}
