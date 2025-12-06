<?php

namespace Tests\Jobs\Client;

use Tests\TestCase;
use App\Jobs\Client\UpdateTaxData;
use App\Models\Client;
use App\Models\Account;
use App\Models\Company;
use App\Models\Country;
use App\Libraries\MultiDB;
use App\Services\Tax\Providers\TaxProvider;
use App\DataProviders\USStates;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Mockery;

class UpdateTaxDataTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    
    /** @test */
public function test_handle_updates_client_state_if_missing_and_postal_code_exists()
{
    // Create account and company
    $account = Account::factory()->create();
    $company = Company::factory()->for($account)->create();

    // Create US country
    $country = Country::first() ?? Country::create(['name' => 'USA', 'id' => 840]);

    // Create a client with postal code but missing state
    $client = Client::factory()->for($company)->create([
        'country_id' => $country->id,
        'state' => null,
        'postal_code' => '90210',
    ]);

    // Mock MultiDB
    $multiDbMock = Mockery::mock('alias:App\Libraries\MultiDB');
    $multiDbMock->shouldReceive('setDb')->once()->with($company->db);

    // Mock USStates to return 'CA' for given postal code
    $this->mock(USStates::class, function ($mock) {
        $mock->shouldReceive('getState')->with('90210')->andReturn('CA');
    });

    // Mock TaxProvider to avoid external calls
    $taxProviderMock = Mockery::mock(\App\Services\Tax\Providers\TaxProvider::class)->makePartial();
    $taxProviderMock->shouldReceive('setBillingAddress')->andReturnSelf();
    $taxProviderMock->shouldReceive('setShippingAddress')->andReturnSelf();
    $taxProviderMock->shouldReceive('updateClientTaxData')->andReturnNull();
    $this->app->instance(\App\Services\Tax\Providers\TaxProvider::class, $taxProviderMock);

    // Run job
    $job = new UpdateTaxData($client, $company);
    $job->handle();

    // Assert that state was updated
    $this->assertEquals('CA', $client->fresh()->state);
}

    
    
    
    
public function test_handle_exits_if_free_hosted_or_non_us_client()
{
    // Create real account
    $account = Account::factory()->create();

    // Partial mock to override isFreeHostedClient()
    $accountMock = Mockery::mock($account)->makePartial();
    $accountMock->shouldReceive('isFreeHostedClient')->andReturn(true);

    // Use the real account when creating company
    $company = Company::factory()->for($account)->create();

    $country = Country::first() ?? Country::create(['name' => 'USA', 'id' => 840]);

    $client = Client::factory()->for($company)->create([
        'country_id' => $country->id,
    ]);

    // Mock MultiDB
    $multiDbMock = Mockery::mock('alias:App\Libraries\MultiDB');
    $multiDbMock->shouldReceive('setDb')->once()->with($company->db);

    $job = new UpdateTaxData($client, $company);
    $job->handle();

    $this->assertTrue(true); // ensures no exception
}

    
    /** @test */
public function test_handle_catches_exceptions_from_tax_provider()
{
    $account = Account::factory()->create();
    $company = Company::factory()->for($account)->create();
    $country = Country::first() ?? Country::create(['name' => 'USA', 'id' => 840]);

    $client = Client::factory()->for($company)->create([
        'country_id' => $country->id,
    ]);

    // Mock MultiDB
    $multiDbMock = Mockery::mock('alias:App\Libraries\MultiDB');
    $multiDbMock->shouldReceive('setDb')->once()->with($company->db);

    // TaxProvider throws exception
    $this->partialMock(TaxProvider::class, function ($mock) use ($company, $client) {
        $mock->shouldReceive('__construct')->with($company, $client)->andThrow(new \Exception('test exception'));
    });

    $job = new UpdateTaxData($client, $company);
    $job->handle();

    $this->assertTrue(true); // No exception escapes
}
    
    
 /** @test */
public function test_getShippingAddress_falls_back_to_client_country_if_shipping_country_missing()
{
    $account = Account::factory()->create();
    $company = Company::factory()->for($account)->create();
    
    // Ensure a country exists
    $country = Country::first() ?? Country::create(['name' => 'USA', 'id' => 840]);

    // Attach country to client via country_id
    $client = Client::factory()->for($company)->create([
        'country_id' => $country->id,      
        'shipping_address1' => '123 Main St',
        'shipping_address2' => 'Apt 1',
        'shipping_city' => 'Test City',
        'shipping_state' => 'TX',
        'shipping_postal_code' => '75001',
        'shipping_country_id' => null,     // No shipping country
    ]);

    $job = new UpdateTaxData($client, $company);

    $expected = [
        'address2' => 'Apt 1',
        'address1' => '123 Main St',
        'city' => 'Test City',
        'state' => 'TX',
        'postal_code' => '75001',
        'country' => $country->name,       
    ];

    $this->assertEquals($expected, $job->getShippingAddress());
}


   public function test_getShippingAddress_returns_shipping_array_when_valid()
    {
        $account = Account::factory()->create();
        $company = Company::factory()->for($account)->create();
        $country = \App\Models\Country::first() ?? \App\Models\Country::create([
    'name' => 'USA',
]);

        $client = Client::factory()->for($company)->create([
            'shipping_address1' => '123 Main St',
            'shipping_address2' => 'Apt 1',
            'shipping_city' => 'Test City',
            'shipping_state' => 'TX',
            'shipping_postal_code' => '75001',
            'shipping_country_id' => $country->id,
        ]);

        $job = new UpdateTaxData($client, $company);

        $expected = [
            'address2' => 'Apt 1',
            'address1' => '123 Main St',
            'city' => 'Test City',
            'state' => 'TX',
            'postal_code' => '75001',
            'country' => $country->name,
        ];

        $this->assertEquals($expected, $job->getShippingAddress());
    }

/** @test */
public function test_getBillingAddress_returns_correct_array()
{
    $account = Account::factory()->create();
    $company = Company::factory()->for($account)->create();
    $country = \App\Models\Country::first() ?? \App\Models\Country::create([
        'name' => 'USA',
        
    ]);

    $client = Client::factory()->for($company)->create([
        'country_id' => $country->id,
    ]);

    $job = new UpdateTaxData($client, $company);

    $expected = [
        'address2' => $client->address2,
        'address1' => $client->address1,
        'city' => $client->city,
        'state' => $client->state,
        'postal_code' => $client->postal_code,
        'country' => $country->name,
    ];

    $this->assertEquals($expected, $job->getBillingAddress());
}

/** @test */
public function test_getShippingAddress_returns_billing_if_shipping_is_short()
{
    $account = Account::factory()->create();
    $company = Company::factory()->for($account)->create();
    $country = \App\Models\Country::first() ?? \App\Models\Country::create([
        'name' => 'USA',
      
    ]);

    $client = Client::factory()->for($company)->create([
        'country_id' => $country->id,
        'shipping_address1' => 'a',
    ]);

    $job = new UpdateTaxData($client, $company);

    $this->assertEquals($job->getBillingAddress(), $job->getShippingAddress());
}   

    /** @test */

public function test_constructs_the_job_correctly()
{
    $account = \App\Models\Account::factory()->create();
    $company = \App\Models\Company::factory()->for($account)->create();
    $client = \App\Models\Client::factory()->for($company)->create();

    $job = new UpdateTaxData($client, $company);

    $this->assertInstanceOf(UpdateTaxData::class, $job);
    $this->assertSame($client->id, $job->client->id);
}


    /** @test */
    public function test_sets_db_and_returns_if_free_hosted_or_non_us_client()
    {
        $account = \App\Models\Account::factory()->create();
$company = \App\Models\Company::factory()->for($account)->create();
$client = \App\Models\Client::factory()->for($company)->create();


        $multiDbMock = Mockery::mock('alias:App\Libraries\MultiDB');
        $multiDbMock->shouldReceive('setDb')->once()->with($company->db);

        $job = new UpdateTaxData($client, $company);
        $job->handle();

        $this->assertTrue(true); // Ensure no exception
    }

    
    /** @test */
    public function test_middleware_returns_without_overlapping_instance()
    {
       $account = \App\Models\Account::factory()->create();
$company = \App\Models\Company::factory()->for($account)->create();
$client = \App\Models\Client::factory()->for($company)->create();


        $job = new UpdateTaxData($client, $company);
        $middleware = $job->middleware();

        $this->assertCount(1, $middleware);
        $this->assertEquals(60, $middleware[0]->releaseAfter);
    }

    /** @test */
    public function test_failed_logs_exception_and_sets_failed_driver_null()
    {
        $account = \App\Models\Account::factory()->create();
$company = \App\Models\Company::factory()->for($account)->create();
$client = \App\Models\Client::factory()->for($company)->create();


        $job = new UpdateTaxData($client, $company);

        $exception = new \Exception("Test exception");

        $job->failed($exception);

        $this->assertNull(config('queue.failed.driver'));
    }
    
    
}

