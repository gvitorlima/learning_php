<?php

namespace App\Controller\Login;

use App\Http\Middlewares\Jwt;
use App\Http\Request;
use App\Http\Response;
use App\Repository\Login\LoginRepository;
use Exception;

class LoginController
{
  private LoginRepository $repository;
  private Response $response;

  public function __construct()
  {
    $this->repository = new LoginRepository;
    $this->response = new Response(200, '');
  }

  public function login(Request $request)
  {
    $patternErr = 'Verifique os dados informados e tente novamente';
    $postVars = $request->getPostVars();

    try {
      $email = $postVars['email'];
      $password = $postVars['password'];

      if (empty($email) || empty($password))
        throw new Exception($patternErr, 400);
      // $hashPassword = $this->hashPassword($password);

      $userData = $this->repository->get($email);
      if (empty($userData) || !password_verify($password, $userData['password']))
        throw new Exception($patternErr, 404);

      $request->setPayload($userData);
      return Jwt::create($request);
    } catch (Exception $err) {
    }
  }

  private function hashPassword(string $password): string
  {
    return password_hash($password, PASSWORD_BCRYPT, [
      'salt' =>  getenv('JWT_SALT')
    ]);
  }
}
