<?php

namespace App\Databases;

use App\Interfaces\iDatabaseConfig;

class LearningAuth implements iDatabaseConfig
{
  const SUFFIX_DATABASE = 'FK_';

  private string
    $dsn, $host, $password, $path, $user;

  private static LearningAuth $instance;

  public static function getInstance(): LearningAuth
  {
    if (!isset(self::$instance))
      self::$instance = new self(self::SUFFIX_DATABASE);

    return self::$instance;
  }

  public function stringConnection(): string
  {
    $stringConnection = 'firebird:dbname=' . $this->host . ':' . $this->path . ';charset=utf-8;dialect=1';
    return $stringConnection;
  }

  public function user(): string
  {
    return $this->user;
  }

  public function password(): string
  {
    return $this->password;
  }

  public function driver(): string
  {
    return $this->dsn;
  }

  private function __construct(string $suffix)
  {
    $this->dsn = getenv($suffix . 'DRIVER');
    $this->host = getenv($suffix . 'HOST');
    $this->password = getenv($suffix . 'PASS');
    $this->path = getenv($suffix . 'PATH');
    $this->user = getenv($suffix . 'USER');
  }
}
