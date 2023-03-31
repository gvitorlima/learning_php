<?php

declare(strict_types=1);

namespace app\Controller\Login;

use app\Http\Middlewares\Jwt;
use app\Http\Request;
use app\Http\Response;
use app\Repository\Login\LoginRepository;
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
    $postVars = $request->getPostVars();

    try {
      $email = (string)$postVars['email'];
      $password = (string)$postVars['password'];

      if (empty($email) || empty($password))
        throw new Exception('Verifique os dados informados e tente novamente', 400);

      $userData = $this->repository->get($email);
      if (empty($userData) || !password_verify($password, $userData['password']))
        throw new Exception('Verifique os dados informados e tente novamente', 404);

      $request->setPayload($userData);
      $this->response
        ->setResponse(200, (new Jwt)->create($request))
        ->sendResponse();
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
