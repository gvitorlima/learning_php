<?php

namespace App\Database\Configs;

use App\Utils\QueryBuilderUtils;
use Exception;

/**
 * Construtor da classe privado, use o método estático "getInstance"
 */
class Query
{
  private static self $selfInstance;

  private array $query;

  private array $comparison = ['=', '>', '<', '>=', '<=', '<>'];

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
      $this->query['SELECT'] = $field;
    } else {

      $concatenated = [];
      foreach ($fields as $field) {
        if (!is_string($field) || empty($field))
          continue;

        $field = QueryBuilderUtils::clearString($field);
        $concatenated[0] = $concatenated[0] . $field . ',';
      }

      $this->query['SELECT'] = substr_replace($concatenated[0], '', -1);
    }
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
    $field = QueryBuilderUtils::clearString($field);
    $value = QueryBuilderUtils::clearString($value);

    if (!in_array($comparison, $this->comparison))
      throw new Exception("Comparador não pode ser usado: $comparison", 400);

    $this->query['WHERE'] = [
      'FIELD' => $field,
      'OPERATOR' => $comparison,
      'VALUE' => $value
    ];
    return self::$selfInstance;
  }

  public function andWhere(string $field, string|int $value, string $comparison = '='): self
  {
    $field = QueryBuilderUtils::clearString($field);
    $value = QueryBuilderUtils::clearString($value);

    if (!in_array($comparison, $this->comparison))
      throw new Exception("Comparador não pode ser usado: $comparison", 400);

    $this->query['AND'][] = [
      'FIELD' => $field,
      'OPERATOR' => $comparison,
      'VALUE' => $value
    ];

    return self::$selfInstance;
  }

  public function run(): array
  {
    $queryAndValues = $this->mountQuery();
    return $queryAndValues;
  }

  private function mountQuery(): array
  {
    $selectQuery = 'SELECT ' . $this->query['SELECT'];
    $fromQuery = 'FROM ' . $this->query['FROM'];

    if ($this->query['WHERE']) {
      $whereQuery = $this->mountWhereQuery([$this->query['WHERE']], 'WHERE');

      if ($this->query['AND']) {
        $andQuery = $this->mountWhereQuery($this->query['AND'], 'AND');
      }
    }

    $query = $selectQuery . ' ' . $fromQuery;
    if ($whereQuery) {
      $values = [];
      $values = $whereQuery['VALUES'];

      $query = $query . ' ' . $whereQuery['QUERY'][0];

      if ($andQuery) {
        $values = array_merge($values, $andQuery['VALUES']);

        $query = $query . ' ' . $andQuery['QUERY'][0];
      }
    }

    return [
      'QUERY' => $query,
      'VALUES' => $values
    ];
  }

  private function mountWhereQuery(array $fieldsAndValues, string $operator): array
  {
    $query = [];
    $values = [];

    foreach ($fieldsAndValues as $value) {
      $query[0] = $query[0] . ' ' . $operator . ' ';
      $query[0] = $query[0] .  $value['FIELD'] . ' ' . $value['OPERATOR']  . ' :' . QueryBuilderUtils::clearAlias($value['FIELD']);

      $values[QueryBuilderUtils::clearAlias($value['FIELD'])] =  $value['VALUE'];
    }

    return [
      "QUERY" => $query,
      'VALUES' => $values
    ];
  }
}
