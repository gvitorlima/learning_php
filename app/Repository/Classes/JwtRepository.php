<?php

namespace app\Repository\Classes;

use app\Database\Configs\Database;
use app\Database\Configs\Query;
use app\Database\LearningAuth;

class JwtRepository
{
  private Query $queryBuilder;

  public function __construct()
  {
    $this->queryBuilder = Query::getInstance();
  }

  public function get(string $email): array
  {
    $query = $this->queryBuilder->select('*', 'AUTH a')
      ->where('a.EMAIL', $email)
      ->getQuery();

    $data = (new Database(LearningAuth::getInstance()))->executeQuery($query);
    return $data[array_key_first($data)] ?? [];
  }
}
