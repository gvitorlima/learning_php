<?php

declare(strict_types=1);

namespace App\Utils;

class QueryBuilderUtils
{
  public static function clearString(string $field): string
  {
    $field = htmlspecialchars($field);
    $field = preg_replace("--", '', $field);

    return $field;
  }

  public static function clearAlias(string $stringWithAlias): string
  {
    $string = str_replace('.', '', $stringWithAlias);
    return $string;
  }
}
