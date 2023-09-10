<?php
/**
 * Copyright 2023 Twinkle Toes Software, LLC
 */

namespace Booked;

use Psr\Http\Message\ResponseInterface;

require ROOT_DIR . 'vendor/autoload.php';

class HttpClient {
    private static function MergeHeaders(array $headers) {
        return array_merge(['Accept' => 'application/json', 'Accept-Language' => 'en_US', 'Content-Type' => 'application/json'], $headers);
    }

    private static function Request(string $method, string $url, array $headers, array $options): ResponseInterface
    {
        $client = new \GuzzleHttp\Client();
        $guzzleOptions = $options;
        $guzzleOptions['verify'] = false;
        $guzzleOptions['headers'] = self::MergeHeaders($headers);
        return $client->request($method, $url, $guzzleOptions);
    }
    public static function Post(string $url, array $headers, array $options): ResponseInterface
    {
        return self::Request('POST', $url, $headers, $options);
    }

    public static function Patch(string $url, array $headers, array $options): ResponseInterface
    {
        return self::Request('PATCH', $url, $headers, $options);
    }

    public static function Get(string $url, array $headers, array $options): ResponseInterface
    {
        return self::Request('GET', $url, $headers, $options);
    }

    public static function Delete(string $url, array $headers, array $options): ResponseInterface
    {
        return self::Request('DELETE', $url, $headers, $options);
    }
}