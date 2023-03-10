<?php

namespace App\Databases;

use App\Interfaces\DatabaseInstance;

class LearningAuth implements DatabaseInstance
{
  private static string
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

  private function __construct()
  {
  }

  private function __clone()
  {
  }

  private function __wakeup()
  {
  }

  public function dsn()
  {
    return self::$dsn;
  }

  public function host()
  {
    return self::$host;
  }

  public function password()
  {
    return self::$password;
  }

  public function path()
  {
    return self::$path;
  }

  public function string()
  {
    return self::$string;
  }

  public function stringConnection()
  {
    return self::$stringConnection;
  }

  public function user()
  {
    return self::$user;
  }
}
