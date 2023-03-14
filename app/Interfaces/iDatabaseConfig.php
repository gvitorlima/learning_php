<?php

namespace App\Interfaces;

interface iDatabaseConfig
{
  //String de conexão com o banco de dados sendo usado
  public function stringConnection();

  //Usuário usado para realizar "login" com o banco de dados em questão
  public function user();

  //Senha do mesmo
  public function password();

  //Driver do banco de dados (firebird, mysql...)
  public function driver();

  //Método estático para pegar a instancia do banco de dados sem precisar usar o operador "new"
  public static function getInstance();
}
