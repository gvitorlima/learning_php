<?php

declare(strict_types=1);

namespace app\Repository\Login;

use app\Database\Configs\Database;
use app\Database\Configs\Query;
use app\Database\LearningAuth;

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
