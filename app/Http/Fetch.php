<?php

namespace App\Http;

use Exception;

class Fetch
{
  public function get(string $url, array $headers = null)
  {
    try {
      $curl = curl_init();
      curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL => 'https://api.jikan.moe/v4/manga/2/full',
      ]);

      return json_decode(curl_exec($curl), true);
    } catch (Exception $err) {
    } finally {
      curl_close($curl);
    }
  }
}
