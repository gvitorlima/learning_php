<?php

declare(strict_types=1);

namespace app\Http\Middlewares;

use app\Exceptions\CacheException;
use app\Http\Request;
use app\Http\Response;
use Closure;
use DateTime;

class Cache extends AbstractMiddleware
{
  public const
    QUERY_PARAM = 'query_param',
    HEADER_PARAM = 'header_param';

  private string
    $fileName,
    $filePath,
    $pathCache;

  private Response $response;

  public function __construct()
  {
    $this->response = new Response(200, '');
  }

  public function handle(Request $request, Closure $next, mixed $params = null)
  {
    try {
      return $this->cache($request, $next, $params);
    } catch (CacheException $error) {
      $error = $this->response->setResponse($error->getCode(), formatResponseError($error));
      $this->response->sendResponse();
    }
  }

  private function cache(Request $request, Closure $next, mixed $params = null)
  {
    if ($params) {
      $dynamicName = match (array_key_first($params)) {
        self::QUERY_PARAM => $request->getQueryParams()[$params[array_key_first($params)]]
      };

      $dynamicName = preg_replace('/\D/', '', $dynamicName);
    }

    $this->fileName = $this->mountName($request->getUri(), $dynamicName);
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

  private function mountName(string $route, string $dynamicName = null)
  {
    $replacePath = str_replace('/', '_', $route);
    if (str_starts_with($replacePath, '_')) {
      $replacePath = substr($replacePath, 1);
    }

    if ($dynamicName)
      $replacePath = $replacePath . '_' . $dynamicName;

    return $replacePath . '.txt';
  }
}
