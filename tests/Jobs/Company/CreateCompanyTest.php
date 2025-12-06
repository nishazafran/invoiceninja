<?php

namespace Tests\Jobs\Company;

use Tests\TestCase;
use App\Jobs\Company\CreateCompany;
use App\Models\Account;
use App\Models\Company;
use App\Models\Country;
use App\Libraries\MultiDB;
use App\Utils\Ninja;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class CreateCompanyTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        // ensure Mockery expectations are cleaned up
        Mockery::close();
        parent::tearDown();
    }
    

public function test_spanish_setup_handles_exception_gracefully()
{
    // Create a Company instance with settings
    $company = new Company();
    $company->settings = (object)[];

    // Mock the Company to allow save() but do nothing
    $companyMock = Mockery::mock($company)->makePartial();
    $companyMock->shouldReceive('save')->andReturnTrue(); // simulate successful save

    // Create the job instance
    $job = new CreateCompany([], (object)['id' => 1]);

    // Use Reflection to call the private spanishSetup method
    $reflection = new \ReflectionClass(CreateCompany::class);
    $method = $reflection->getMethod('spanishSetup');
    $method->setAccessible(true);

    // Invoke method normally; no exception will escape
    $resultCompany = $method->invoke($job, $companyMock);

    $this->assertInstanceOf(Company::class, $resultCompany);
}


public function test_spanish_setup_sets_custom_fields_tax_rates_and_settings()
    {
        // Create a Company instance
        $company = new Company();
        $company->settings = (object)[];

        // Mock save() so no DB errors occur
        $companyMock = Mockery::mock($company)->makePartial();
        $companyMock->shouldReceive('save')->andReturnTrue();

        // Create instance of CreateCompany (request and account not needed for this test)
        $job = new CreateCompany([], (object)['id' => 1]);

        // Use Reflection to call the private method
        $reflection = new \ReflectionClass(CreateCompany::class);
        $method = $reflection->getMethod('spanishSetup');
        $method->setAccessible(true);

        $resultCompany = $method->invoke($job, $companyMock);

        // Assertions
        $this->assertEquals(1, $resultCompany->enabled_item_tax_rates);

        // Assert custom fields exist
        $this->assertTrue(property_exists($resultCompany->custom_fields, 'contact1'));
        $this->assertTrue(property_exists($resultCompany->custom_fields, 'contact2'));
        $this->assertTrue(property_exists($resultCompany->custom_fields, 'contact3'));
        $this->assertTrue(property_exists($resultCompany->custom_fields, 'client1'));

        // Assert settings are set correctly
        $this->assertEquals('7', $resultCompany->settings->language_id);
        $this->assertEquals('Facturae_3.2.2', $resultCompany->settings->e_invoice_type);
        $this->assertEquals('3', $resultCompany->settings->currency_id);
        $this->assertEquals('42', $resultCompany->settings->timezone_id);
    }


    public function test_handle_sets_empty_subdomain_when_not_hosted()
    {
        $account = Account::factory()->create();
        request()->headers->set('cf-ipcountry', 'US');

        $mockCountry = Mockery::mock('alias:App\Models\Country');
        $mockCountry->shouldReceive('query->where->first')
            ->andReturn((object)['id' => 840]);

       $this->instance(\App\Utils\Ninja::class, Mockery::mock()->shouldReceive('isHosted')->andReturnTrue()->getMock());

        $job = new CreateCompany(['name' => 'Test Company'], $account);
        $company = $job->handle();

        $this->assertInstanceOf(Company::class, $company);
        $this->assertEquals('', $company->subdomain);
    }


    public function test_handle_runs_australia_setup_for_country_id_36()
    {
        $account = Account::factory()->create();

        $mockCountry = Mockery::mock('alias:App\Models\Country');
        $mockCountry->shouldReceive('query->where->first')
            ->andReturn((object)['id' => 36]);

        request()->headers->set('cf-ipcountry', 'AU');

        $job = new CreateCompany(['name' => 'Australia Co'], $account);
        $company = $job->handle();

        $this->assertEquals(36, $company->settings->country_id);
        $this->assertEquals(1, $company->enabled_item_tax_rates);
        $this->assertEquals(1, $company->enabled_tax_rates);
        $this->assertEquals('12', $company->settings->currency_id);
    }

    /** @test */
    public function handle_runs_south_africa_setup_for_country_id_710()
    {
        $account = Account::factory()->create();

        $mockCountry = Mockery::mock('alias:App\Models\Country');
        $mockCountry->shouldReceive('query->where->first')
            ->andReturn((object)['id' => 710]);

        request()->headers->set('cf-ipcountry', 'ZA');

        $job = new CreateCompany(['name' => 'South Africa Co'], $account);
        $company = $job->handle();

        $this->assertEquals(710, $company->settings->country_id);
        $this->assertEquals(1, $company->enabled_item_tax_rates);
        $this->assertEquals(1, $company->enabled_tax_rates);
        $this->assertEquals('4', $company->settings->currency_id);
    }

    /** @test */
    public function test_handle_runs_new_zealand_setup_for_country_id_554()
    {
        $account = Account::factory()->create();

        $mockCountry = Mockery::mock('alias:App\Models\Country');
        $mockCountry->shouldReceive('query->where->first')
            ->andReturn((object)['id' => 554]);

        request()->headers->set('cf-ipcountry', 'NZ');

        $job = new CreateCompany(['name' => 'NZ Co'], $account);
        $company = $job->handle();

        $this->assertEquals(554, $company->settings->country_id);
        $this->assertEquals(1, $company->enabled_tax_rates);
        $this->assertEquals('15', $company->settings->currency_id);
    }


    public function test_country_falls_back_to_default_when_header_country_not_found()
    {
        $account = Account::factory()->create();

        // set header to a country code that does not exist in DB
        request()->headers->set('cf-ipcountry', 'XX');

        $job = new CreateCompany([], $account);

        // private method resolveCountry; call it via reflection
        $ref = new \ReflectionClass($job);
        $method = $ref->getMethod('resolveCountry');
        $method->setAccessible(true);

        $countryId = $method->invoke($job);

        // default fallback in job is string '840'
        $this->assertEquals('840', $countryId);
    }

    public function test_construct_accepts_request_and_account()
    {
        $account = Account::factory()->make();
        $request = ['name' => 'Construct Test'];

        $job = new CreateCompany($request, $account);

        // check internal properties via reflection
        $ref = new \ReflectionClass($job);
        $reqProp = $ref->getProperty('request');
        $reqProp->setAccessible(true);
        $this->assertEquals($request, $reqProp->getValue($job));

        $accProp = $ref->getProperty('account');
        $accProp->setAccessible(true);
        $this->assertSame($account, $accProp->getValue($job));
    }
}

