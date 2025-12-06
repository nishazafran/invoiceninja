<?php

namespace Tests\Utils;

use App\Utils\SystemHealth;
use App\Libraries\MultiDB;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\Queue;

class SystemHealthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Mock DB connections
        DB::shouldReceive('connection')->andReturnSelf();
        DB::shouldReceive('getPdo')->andReturn(true);
        DB::shouldReceive('getDatabaseName')->andReturn('test_db');

        // Mock config for PDF engines, phantom, mail
        Config::shouldReceive('get')->andReturnMap([
            ['ninja.phantomjs_pdf_generation', false],
            ['ninja.pdf_generator', 'snap'],
            ['ninja.phantomjs_pdf_generation', false],
            ['ninja.invoiceninja_hosted_pdf_generation', false],
            ['mail.default', 'smtp'],
            ['ninja.currency_converter_api_key', ''],
            ['ninja.flutter_canvas_kit', 'canvas'],
            ['queue.default', 'default'],
            ['ninja.app_version', '1.0.0'],
            ['ninja.is_docker', false],
            ['ninja.db.multi_db_enabled', false],
        ]);
    }

    public function test_extensions_method_returns_expected_structure()
    {
        $extensions = (new \ReflectionClass(SystemHealth::class))
            ->getMethod('extensions')
            ->invoke(null);

        $this->assertIsArray($extensions);
        $this->assertNotEmpty($extensions);
    }

    public function test_check_returns_array_with_true_system_health()
    {
        $result = SystemHealth::check();
        $this->assertArrayHasKey('system_health', $result);
        $this->assertTrue($result['system_health']);
        $this->assertArrayHasKey('php_version', $result);
        $this->assertArrayHasKey('extensions', $result);
    }

    public function test_checkCurrencySanity_returns_true_if_multiple_currencies()
    {
        DB::shouldReceive('table')->andReturnSelf();
        DB::shouldReceive('select')->andReturn(collect([(object)['id'=>1], (object)['id'=>2]]));
        DB::shouldReceive('get')->andReturn(collect([(object)['id'=>1], (object)['id'=>2]]));

        $method = (new \ReflectionClass(SystemHealth::class))
            ->getMethod('checkCurrencySanity');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke(null));
    }

    public function test_checkQueueData_returns_array_with_counts()
    {
        DB::shouldReceive('table')->with('jobs')->andReturnSelf();
        DB::shouldReceive('count')->andReturn(5);
        DB::shouldReceive('table')->with('failed_jobs')->andReturnSelf();
        DB::shouldReceive('count')->andReturn(1);
        DB::shouldReceive('latest')->andReturnSelf();
        DB::shouldReceive('first')->andReturn((object)['exception'=>'error message']);

        $method = (new \ReflectionClass(SystemHealth::class))
            ->getMethod('checkQueueData');
        $method->setAccessible(true);

        $data = $method->invoke(null);
        $this->assertEquals(5, $data['pending']);
        $this->assertEquals(1, $data['failed']);
        $this->assertEquals('error message', $data['last_error']);
    }

    public function test_checkFileSystem_returns_ok()
    {
        $result = SystemHealth::checkFileSystem();
        $this->assertIsString($result);
    }

    public function test_checkUrlState_true_false()
    {
        putenv('APP_URL=http://localhost/');
        $this->assertTrue(SystemHealth::checkUrlState());

        putenv('APP_URL=http://localhost');
        $this->assertFalse(SystemHealth::checkUrlState());
    }

    public function test_checkPendingMigrations_true_false()
    {
        DB::shouldReceive('table')->with('migrations')->andReturnSelf();
        DB::shouldReceive('count')->andReturn(5);

        $result = SystemHealth::checkPendingMigrations();
        $this->assertIsBool($result);
    }

    public function test_getPdfEngine_returns_expected_values()
    {
        $this->assertEquals('SnapPDF PDF Generator', SystemHealth::getPdfEngine());

        Config::shouldReceive('get')->with('ninja.invoiceninja_hosted_pdf_generation')->andReturn(true);
        $this->assertEquals('Invoice Ninja Hosted PDF Generator', SystemHealth::getPdfEngine());

        Config::shouldReceive('get')->with('ninja.invoiceninja_hosted_pdf_generation')->andReturn(false);
        Config::shouldReceive('get')->with('ninja.pdf_generator')->andReturn('phantom');
        $this->assertEquals('Phantom JS Web Generator', SystemHealth::getPdfEngine());
    }

    public function test_checkMailMailer_returns_string()
    {
        $this->assertEquals('smtp', SystemHealth::checkMailMailer());
    }

    public function test_checkOpenBaseDir_true_false()
    {
        ini_set('open_basedir', '');
        $this->assertTrue(SystemHealth::checkOpenBaseDir());

        ini_set('open_basedir', '/tmp');
        $this->assertFalse(SystemHealth::checkOpenBaseDir());
    }

    public function test_checkExecWorks_true_false()
    {
        $this->assertTrue(SystemHealth::checkExecWorks());
    }

    public function test_checkConfigCache_true_false()
    {
        putenv('APP_URL');
        $this->assertTrue(SystemHealth::checkConfigCache());

        putenv('APP_URL=http://localhost');
        $this->assertFalse(SystemHealth::checkConfigCache());
    }

    public function test_simpleDbCheck_returns_true_false()
    {
        $method = (new \ReflectionClass(SystemHealth::class))
            ->getMethod('simpleDbCheck');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke(null));
    }

    public function test_checkPhpCli_returns_string_or_false()
    {
        $method = (new \ReflectionClass(SystemHealth::class))
            ->getMethod('checkPhpCli');
        $method->setAccessible(true);

        $result = $method->invoke(null);
        $this->assertIsString($result);
    }

    public function test_dbCheck_returns_success()
    {
        $result = SystemHealth::dbCheck();
        $this->assertTrue($result['success']);
    }

    public function function_test_testMailServer()
    {
        $request = new class {
            public $driver = 'smtp';
            public function input($key) {
                return 'test';
            }
        };

        Mail::fake();

        $result = SystemHealth::testMailServer($request);
        $this->assertTrue($result['success']);
    }

    public function test_lastError_returns_string()
    {
        $result = SystemHealth::lastError();
        $this->assertIsString($result);
    }
}

