<?php

declare(strict_types=1);

namespace App\Repository\Example;

use App\Database\Configs\Database;
use App\Database\Configs\Query;
use App\Database\LearningAuth;

class ExampleMiddlewaresRepository
{
  private Database $database;
  private Query $queryBuilder;

  public function __construct()
  {
    $this->database = new Database(LearningAuth::getInstance());
    $this->queryBuilder = Query::getInstance();
  }

  public function getCacheData()
  {
    $query = $this->queryBuilder->select('*', 'AUTH a')
      ->where('a.EMAIL', 'teste@teste.com')
      ->andWhere('a.PASSWORD', '$2y$10$11LZ/6f1yPR.swShB3io3O8mcsBDH0QkgpF7CDXO2mldgFenGnzPa')
      ->getQuery();

    return $this->database->executeQuery($query);
  }
}
