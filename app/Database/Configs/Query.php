<?php

namespace App\Database\Configs;

use App\Utils\QueryBuilderUtils;

/**
 * Construtor da classe privado, use o método estático "getInstance"
 */
class Query
{
  private static self $selfInstance;

  private array $query;

  private array $comparison = ['=', '>', '<', '>=', '<=', '<>', 'between'];

  private function __construct()
  {
  }

  public static function getInstance(): self
  {
    if (!isset(self::$selfInstance))
      self::$selfInstance = new self();

    return self::$selfInstance;
  }

  // Caso fields seja um array, só serão consideradas as chaves
  public function select(array|string $fields = '*'): self
  {
    if (is_string($fields)) {
      $field = QueryBuilderUtils::clearString($fields);
      $this->query['SELECT'] = 'SELECT ' . $field;
    }

    $concatenated = [];
    foreach ($fields as $field) {
      if (!is_string($field) || empty($field))
        continue;

      $field = QueryBuilderUtils::clearString($field);
      $concatenated[0] = $concatenated[0] . $field . ',';
    }

    $this->query['SELECT'] = 'SELECT ' . substr_replace($concatenated[0], '', -1);
    return self::$selfInstance;
  }

  public function from(string $field): self
  {
    $this->query['FROM'] = QueryBuilderUtils::clearString($field);
    return self::$selfInstance;
  }

  /**
   * @param $field, Campo no banco
   * @param $value, Valor a ser comprado
   * @param $comparison, Operador de comparação
   */
  public function where(string $field, string|int $value, string $comparison = '='): self
  {
    $field =
      $value =

      $this->query['WHERE'] = 'WHERE ' . $field . ' = ' . $value;
    return self::$selfInstance;
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
    return self::$selfInstance;
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

  private function mountArrayFields(array $fields, string $clauses = null)
  {
    // $concatenated = [];

    // foreach ($fields as $key => $value) {
    //   $concatenated[0] = $key $value;
    // }

    // echo '<pre>';
    // print_r($concatenated);
    // echo '</pre>';
    // exit;
  }
}
