<?php

namespace App\Utils;

class QueryBuilderUtils
{
  public static function clearString(string $field): string
  {
    $field = htmlspecialchars($field);
    $field = preg_replace("/((?:\W)(?<!-))/", '', $field);

    return $field;
  }
}
