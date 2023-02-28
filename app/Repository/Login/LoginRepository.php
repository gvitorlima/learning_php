<?php

namespace App\Repository\Login;

use App\Database\Database;

class LoginRepository
{
  private Database $database;

  public function __construct()
  {
    $this->database = new Database('DB_');
  }

  public function get(string $email)
  {
    $sql = "SELECT * FROM AUTH a WHERE a.EMAIL = '$email'";
    $data = $this->database->executeQuery($sql);

    return $data[array_key_first($data)] ?? [];
  }
}
