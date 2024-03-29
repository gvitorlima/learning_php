<?php

namespace app\Controller\Classes;

use app\Http\Middlewares\Jwt;
use app\Http\Request;
use app\Http\Response;
use app\Repository\Classes\JwtRepository;
use Exception;

class JwtController
{
  private JwtRepository $repository;
  private Response $response;

  public function __construct()
  {
    $this->repository = new JwtRepository;
    $this->response = new Response(200, '');
  }

  public function create(Request $request)
  {
    try {
      $postVars = $request->getPostVars();

      $password = (string)$postVars['password'];
      $email = $postVars['email'];

      $userData = $this->repository->get($email);
      if (empty($userData) || !password_verify($password, $userData['password']))
        throw new Exception('Verifique os dados informados e tente novamente', 404);

      $request->setPayload($userData);
      return $this->response
        ->setResponse(200, (new Jwt)->create($request))
        ->sendResponse();
    } catch (Exception $err) {
    }
  }
}
