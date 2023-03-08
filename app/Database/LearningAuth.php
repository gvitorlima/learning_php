<?php

namespace App\Database;

use App\Interfaces\DatabaseInterface;

class LearningAuth implements DatabaseInterface
{
  public static string
    $dsn,
    $host,
    $password,
    $path,
    $string,
    $stringConnection,
    $user;

  private static self
    $instance;

  public static function getInstance()
  {
    if (isset(self::$instance))
      return self::$instance;

    self::init();
    return self::$instance = new self;
  }

  private static function init(string $suffix = 'FK_')
  {
    self::$dsn = getenv($suffix . 'DRIVER');
    self::$host = getenv($suffix . 'HOST');
    self::$password = getenv($suffix . 'PASS');
    self::$path = getenv($suffix . 'PATH');
    self::$user = getenv($suffix . 'USER');
  }
}
