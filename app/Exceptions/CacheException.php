<?php

declare(strict_types=1);

namespace app\Exceptions;

use Exception;

class CacheException extends Exception
{
  public function __construct()
  {
    parent::__construct($this->getMessage(), $this->getCode());
  }
}
