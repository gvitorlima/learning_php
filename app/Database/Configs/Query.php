<?php

declare(strict_types=1);

namespace App\Database\Configs;

use App\Utils\QueryBuilderUtils;
use App\Database\Configs\MountQuery;
use Exception;

/**
 * Construtor da classe privado, use o método estático "getInstance"
 */
class Query
{
  private MountQuery $mountQuery;
  private static self $selfInstance;

  private array $query = [];


  private array $comparison = ['=', '>', '<', '>=', '<=', '<>'];

  private function __construct()
  {
    $this->mountQuery = new MountQuery;
  }

  public static function getInstance(): self
  {
    if (!isset(self::$selfInstance))
      self::$selfInstance = new self();

    return self::$selfInstance;
  }

  // Caso fields seja um array, só serão consideradas as chaves
  public function select(array|string $fields, string $table): self
  {
    if (is_string($fields)) {
      $field = QueryBuilderUtils::clearString($fields);
      $this->query['SELECT'] = [
        'TABLE' => $table,
        'FIELDS' => $field
      ];
    } else {
      foreach ($fields as $field) {
        if (!is_string($field) || empty($field))
          continue;

        $clearFields[] = QueryBuilderUtils::clearString($field);
      }

      $this->query['SELECT'] = [
        'TABLE' => $table,
        'FIELDS' => $clearFields
      ];
    }

    return self::$selfInstance;
  }

  public function update(string $table, string $field, int|string $value): self
  {
    $this->query['UPDATE'] = [
      $field => QueryBuilderUtils::clearString($value)
    ];
    return $this->selfInstance;
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

  public function innerJoin(string $table, string $innerTable, string $comparison = '='): self
  {
    $this->query['INNER JOIN'][] = [
      'TABLE' => $table,
      'INNER TABLE' => $innerTable,
      'COMPARISON' => $comparison
    ];

    return $this->selfInstance;
  }

  public function getQuery(): array
  {
    $queryAndValues = $this->mountQuery();
    return $queryAndValues;
  }

  /**
   * * A query é montada de acordo a ordem de chamada das funções. Logo, possíveis queries erradas podem ser montadas. CUIDADO
   */
  private function mountQuery(): array
  {
    $this->__construct();
    foreach ($this->query as $key => $_) {

      $arrayQueries[] = match ($key) {
        'SELECT' => $this->mountQuery->select($this->query[$key]),
        'UPDATE' => $this->mountQuery->update($this->query[$key]),
        'WHERE'  => $this->mountQuery->where($this->query[$key]),
        'AND'    => $this->mountQuery->and($this->query[$key]),
        'INNER JOIN' => $this->mountQuery->innerJoin($this->query[$key]),
      };
    }

    $stringQuery = '';
    foreach ($arrayQueries as $key => $query) {
      $stringQuery = $stringQuery . $query . ' ';
    }
    $stringQuery = trim($stringQuery);
    $valuesQuery = $this->mountQuery->getValues();

    return [
      'QUERY' => $stringQuery,
      'VALUES' => $valuesQuery['VALUES']
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
