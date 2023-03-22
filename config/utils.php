<?php

declare(strict_types=1);

function formatResponse(int $code, mixed $content = null, bool $json = false): string|array
{
  $return = [
    'code' => $code,
    'content' => $content
  ];

  if (!$json) {
    return $return;
  }

  return json_encode($return);
}

function formatResponseError(Throwable $error): array
{
  return [
    'code' => $error->getCode(),
    'message' => $error->getMessage()
  ];
}
