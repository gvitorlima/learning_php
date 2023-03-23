<?php

namespace App\Repository\Classes;

use App\Database\Configs\Database;
use App\Database\Configs\Query;
use App\Database\LearningAuth;

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
