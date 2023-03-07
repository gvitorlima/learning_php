<?php

namespace App\Http\Middlewares;

use App\Http\Request;
use App\Http\Response;
use Closure;
use DateTime;
use Exception;

class Cache extends AbstractMiddleware
{
  private string
    $fileName,
    $filePath,
    $pathCache;

  public function handle(Request $request, Closure $next)
  {
    try {
      return $this->cache($request, $next);
    } catch (Exception $err) {
      echo '<pre>';
      print_r($err->getMessage());
      echo '</pre>';
      exit;
    }
  }

  public function cache(Request $request, Closure $next)
  {
    $this->fileName = $this->mountName($request->getUri());
    $this->pathCache = $request->getRootPath() . '/../app/Cache/';
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

    if ($modificationTime->i >= 30 || $modificationTime->days >= 1)
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
