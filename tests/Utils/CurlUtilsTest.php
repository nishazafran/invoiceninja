<?php

namespace Tests\Utils;

use App\Utils\CurlUtils;
use Tests\TestCase;

class CurlUtilsTest extends TestCase
{
    public function test_post_executes_and_returns_response()
    {
        $url = 'https://httpbin.org/post';
        $data = ['foo' => 'bar'];
        
        $response = CurlUtils::post($url, json_encode($data), ['Content-Type: application/json']);

        $this->assertIsString($response);
        $this->assertStringContainsString('"foo": "bar"', $response);
    }

    public function test_get_executes_and_returns_response()
    {
        $url = 'https://httpbin.org/get';
        
        $response = CurlUtils::get($url);

        $this->assertIsString($response);
        $this->assertStringContainsString('"url": "https://httpbin.org/get"', $response);
    }

    public function test_exec_method_handles_invalid_url_and_logs_error()
    {
        $invalidUrl = 'https://invalid.url.test';

        $response = CurlUtils::exec('GET', $invalidUrl, null);

        // curl_exec should return false on invalid URL
        $this->assertFalse($response);
    }
}

