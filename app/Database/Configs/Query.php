<?php

namespace App\Database\Configs;

use App\Database\Configs\Database;
use App\Interfaces\iDatabaseConfig;

class Query
{
  private Database $database;

  private static self $selfInstance;

  private array $query;

  public function __construct(iDatabaseConfig $instance)
  {
    $this->database = new Database($instance);
    if (!isset($selfInstance))
      $this->selfInstance = new self($instance);
  }

  public function select(array|string $fields = '*'): self
  {
    $this->query[] = 'SELECT ' . $fields;
    return $this->selfInstance;
  }

  public function from(string $field): self
  {
    $this->query[] = 'FROM ' . $field;
    return $this->selfInstance;
  }

  public function where(array|string $fieldsAndValues): self
  {
    if (is_array($fieldsAndValues)) {
      $concatenated = [];

      foreach ($fieldsAndValues as $key => $value) {
        $string = $key . ' = ' . $value;

        $concatenated[0] = $concatenated[0] . $string;
      }
    }

    $this->query[] = ' AND ' . $concatenated[0] ?? $fieldsAndValues;
    return $this->selfInstance;
  }

  public function andWhere(array|string $fieldsAndValues): self
  {
    if (is_array($fieldsAndValues)) {
      $concatenated = [];

      foreach ($fieldsAndValues as $key => $value) {
        $string = $key . ' = ' . $value;

        $concatenated[0] = $concatenated[0] . $string;
      }
    }

    $this->query[] = ' AND ' . $concatenated[0] ?? $fieldsAndValues;
    return $this->selfInstance;
  }

  public function run(): void
  {
    $sql = $this->mountQuery();
    echo '<pre>';
    print_r($sql);
    echo '</pre>';
    exit;
  }

  private function mountQuery(): string
  {
    $concatenated = [];

    foreach ($this->query as $key => $value) {
      $concatenated[0] = $concatenated[0] . $value . ' ';
    }

    return $concatenated[0];
  }
}
