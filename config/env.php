<?php

$path = __DIR__ . '/../.env';

$file = file($path, FILE_SKIP_EMPTY_LINES);
foreach ($file as $line) {
  if (str_starts_with($line, '#')) {
    continue;
  }

  putenv(trim($line));
}
