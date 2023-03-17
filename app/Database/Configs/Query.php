<?php

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

  private array $query = [
    'QUERY' => null,
    'VALUES' => null
  ];

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
      $this->query['QUERY']['SELECT'] = [
        'TABLE' => $table,
        'FIELDS' => $field
      ];
    } else {
      foreach ($fields as $field) {
        if (!is_string($field) || empty($field))
          continue;

        $clearFields[] = QueryBuilderUtils::clearString($field);
      }

      $this->query['QUERY']['SELECT'] = [
        'TABLE' => $table,
        'FIELDS' => $clearFields
      ];
    }

    return self::$selfInstance;
  }

  public function update(string $table, string $field, int|string $value): self
  {
    $this->query['QUERY']['UPDATE'] = [
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

    $this->query['QUERY']['WHERE'] = [
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

    $this->query['QUERY']['AND'][] = [
      'FIELD' => $field,
      'OPERATOR' => $comparison,
      'VALUE' => $value
    ];

    return self::$selfInstance;
  }

  public function innerJoin(string $table, string $innerTable, string $comparison = '='): self
  {
    $this->query['QUERY']['INNER JOIN'][] = [
      'TABLE' => $table,
      'INNER TABLE' => $innerTable,
      'COMPARISON' => $comparison
    ];

    return $this->selfInstance;
  }

  public function run(): array
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
    foreach ($this->query['QUERY'] as $key => $_) {

      match ($key) {
        'SELECT' => $this->mountQuery->select($this->query['QUERY']['SELECT']),
        'UPDATE' => $this->mountQuery->update($this->query['QUERY']['UPDATE']),
        'WHERE' => $this->mountQuery->where($this->query['QUERY']['WHERE']),
        'AND' => $this->mountQuery->and($this->query['QUERY']['AND']),
        'INNER JOIN' => $this->mountQuery->innerJoin($this->query['QUERY']['INNER JOIN']),
      };
    }
    echo '<pre>';
    // print_r($this->query);
    echo '</pre>';
    exit;
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
