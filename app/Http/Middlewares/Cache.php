<?php

namespace App\Http\Middlewares;

use App\Exceptions\CacheException;
use App\Http\Request;
use App\Http\Response;
use Closure;
use DateTime;

class Cache extends AbstractMiddleware
{
  private string
    $fileName,
    $filePath,
    $pathCache;

  private Response $response;

  public function __construct()
  {
    $this->response = new Response(200, '');
  }

  public function handle(Request $request, Closure $next)
  {
    try {
      return $this->cache($request, $next);
    } catch (CacheException $error) {
      $error = $this->response->setResponse($error->getCode(), formatResponseError($error));
      $this->response->sendResponse();
    }
  }

  private function cache(Request $request, Closure $next)
  {
    $this->fileName = $this->mountName($request->getUri());
    $this->pathCache = $request->getRootPath() . '/../app/Storage/Cache/';
    if (!$this->validateCache()) {
      $data = $next($request);
      $this->create($data);

      return $data;
    }

    $data = file_get_contents($this->filePath);
    return unserialize($data);
  }

  private function create(mixed $data)
  {
    if (!file_exists($this->pathCache))
      mkdir($this->pathCache, 0755);

    if (!file_exists($this->filePath))
      readfile($this->filePath);

    $serializeData = serialize($data);
    file_put_contents($this->filePath, $serializeData);
    touch($this->filePath);
  }

  private function validateCache(): bool
  {
    $this->filePath = $this->pathCache . $this->fileName;
    if (!file_exists($this->filePath))
      return false;

    $modificationTime =  date('Y-m-d H:i', filemtime($this->filePath));
    $modificationTime = (new DateTime($modificationTime))->diff((new DateTime()));

    if ($modificationTime->i >= 30 || $modificationTime->h >= 1 || $modificationTime->days >= 1)
      return false;

    return true;
  }

  private function mountName(string $route)
  {
    $replacePath = str_replace('/', '_', $route);
    if (str_starts_with($replacePath, '_')) {
      $replacePath = substr($replacePath, 1);
    }

    return $replacePath . '.txt';
  }
}
