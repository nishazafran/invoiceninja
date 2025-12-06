<?php

namespace Tests\Utils;

use App\Utils\Ninja;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\App;
use PHPUnit\Framework\TestCase;

class NinjaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Set up Laravel facades mocks for config, DB, Http, request, App
        Config::shouldReceive('get')->andReturnNull();
        DB::shouldReceive('select')->andReturn([(object)['version' => '8.0']]);
    }

    public function test_isSelfHost_and_isHosted_and_isNinjaDev()
    {
        Config::shouldReceive('get')->with('ninja.environment')->andReturn('selfhost');
        $this->assertTrue(Ninja::isSelfHost());

        Config::shouldReceive('get')->with('ninja.environment')->andReturn('hosted');
        $this->assertTrue(Ninja::isHosted());

        Config::shouldReceive('get')->with('ninja.environment')->andReturn('development');
        $this->assertTrue(Ninja::isNinjaDev());
    }

    public function test_isNinja_checks_production_config()
    {
        Config::shouldReceive('get')->with('ninja.production')->andReturn(true);
        $this->assertTrue(Ninja::isNinja());
    }

    public function test_getDebugInfo_returns_expected_string()
    {
        $request = $this->createMock(\Illuminate\Http\Request::class);
        $request->method('input')->willReturn('1.0.0');
        app()->instance('request', $request);

        Config::shouldReceive('get')->with('ninja.app_version')->andReturn('1.0.0');

        $info = Ninja::getDebugInfo();
        $this->assertStringContainsString('App Version: v1.0.0', $info);
        $this->assertStringContainsString('MySQL Version: 8.0', $info);
    }

    public function test_boot_returns_true_in_dev_and_false_in_production()
    {
        Config::shouldReceive('get')->with('ninja.environment')->andReturn('development');
        $this->assertTrue(Ninja::boot());

        Config::shouldReceive('get')->with('ninja.environment')->andReturn('production');
        Config::shouldReceive('get')->with('ninja.license')->andReturn('LICENSE');

        // Fake CurlUtils::post call
        if (!class_exists('App\Utils\CurlUtils')) {
            eval('namespace App\Utils; class CurlUtils { public static function post($url, $data) { return json_encode(["message"=>"wrong"]); } }');
        }

        $this->assertFalse(Ninja::boot());
    }

    public function test_parse_returns_invalid_license_string()
    {
        $this->assertEquals('Invalid license.', Ninja::parse());
    }

    public function test_selfHostedMessage_returns_expected_string()
    {
        $this->assertEquals('Self hosted installation limited to one account', Ninja::selfHostedMessage());
    }

    public function test_registerNinjaUser_returns_false_for_test_user()
    {
        $user = (object)['email' => Ninja::TEST_USERNAME, 'first_name' => 'A', 'last_name' => 'B'];
        $this->assertFalse(Ninja::registerNinjaUser($user));
    }

    public function test_eventVars_returns_ip_and_token()
    {
        $request = $this->createMock(\Illuminate\Http\Request::class);
        $request->method('hasHeader')->willReturn(false);
        $request->method('ip')->willReturn('127.0.0.1');
        $request->method('header')->willReturn('TOKEN');
        app()->instance('request', $request);

        App::shouldReceive('runningInConsole')->andReturn(false);

        $vars = Ninja::eventVars(5);
        $this->assertEquals('127.0.0.1', $vars['ip']);
        $this->assertEquals(5, $vars['user_id']);
        $this->assertEquals('TOKEN', $vars['token']);
    }

    public function test_transformTranslations_returns_expected_array()
    {
        $settings = (object)['translations' => []];
        $this->assertEquals([], Ninja::transformTranslations($settings));

        $settings = (object)['translations' => ['welcome' => 'Hello']];
        $result = Ninja::transformTranslations($settings);
        $this->assertArrayHasKey('texts.welcome', $result);
        $this->assertEquals('Hello', $result['texts.welcome']);
    }

    public function test_isBase64Encoded_checks_various_cases()
    {
        $valid = base64_encode('Hello');
        $invalid = 'Hello!@#';

        $this->assertTrue(Ninja::isBase64Encoded($valid));
        $this->assertFalse(Ninja::isBase64Encoded($invalid));
    }

    public function test_triggerForwarding_executes_without_exception()
    {
        Config::shouldReceive('get')->with('ninja.ninja_hosted_secret')->andReturn('SECRET');
        Config::shouldReceive('get')->with('ninja.license_url')->andReturn('https://example.com');

        Http::fake();
        Ninja::triggerForwarding('KEY', 'test@example.com');

        $this->assertTrue(true); // if no exception, test passes
    }
}

