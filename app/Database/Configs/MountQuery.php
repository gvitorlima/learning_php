<?php

declare(strict_types=1);

namespace app\Database\Configs;

use app\Utils\QueryBuilderUtils;

class MountQuery
{
  private array $values = [
    'VALUES' => null
  ];
  public function select(array $query): string
  {
    if (is_array($query['FIELDS'])) {
      $field = [];
      foreach ($query['FIELDS'] as $key) {
        $field[0] = $field[0] . $key . ', ';
      }

      $field[0] = trim($field[0]);
      if (str_ends_with($field[0], ','))
        $field = substr($field[0], 1, (strlen($field[0]) - 2));
    }

    $stringQuery = 'SELECT ' . ($field ?? $query['FIELDS']) . ' FROM ' . $query['TABLE'];
    return $stringQuery;
  }

  // TODO ESSE NEGÓCIOS ABAIXO
  public function update(array $query)
  {
  }

  // TODO ESSE NEGÓCIOS ABAIXO
  public function from(array $query)
  {
  }

  public function where(array $query): string
  {
    $this->values['VALUES'][] = $query['VALUE'];
    $stringQuery = 'WHERE ' . $query['FIELD'] . ' ' . $query['OPERATOR'] . ' :' . QueryBuilderUtils::clearAlias($query['FIELD']);
    return $stringQuery;
  }

  public function and(array $query): string
  {
    $stringQuery = '';
    foreach ($query as $_ => $keys) {
      $stringQuery = $stringQuery . ' AND ' . $keys['FIELD'] . ' ' . $keys['OPERATOR'] . ' :' . QueryBuilderUtils::clearAlias($keys['FIELD']);
      $stringQuery = trim($stringQuery);

      $this->values['VALUES'][] = $keys['VALUE'];
    }

    return $stringQuery;
  }

  // TODO ESSE NEGÓCIOS ABAIXO
  public function innerJoin(array $query)
  {
  }

  public function getValues(): array
  {
    return $this->values;
  }
}
