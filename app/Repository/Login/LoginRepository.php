<?php

namespace App\Repository\Login;

use App\Databases\Database;
use App\Databases\enumPrefix;
use App\Databases\LearningAuth;

class LoginRepository
{
  private Database $database;
  public function __construct()
  {
    $this->database = new Database(LearningAuth::getInstance());
  }

  public function get(string $email)
  {
    $sql = "SELECT * FROM AUTH a WHERE a.EMAIL = '$email'";
    $data = $this->database->executeQuery($sql);

    return $data[array_key_first($data)] ?? [];
  }
}
