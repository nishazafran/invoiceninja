<?php

namespace Tests\Jobs\Client;

use Tests\TestCase;
use App\Jobs\Client\UpdateLocationTaxData;
use App\Models\Company;
use App\Models\Location;
use App\Models\Client;
use App\Models\Account;
use App\Libraries\MultiDB;
use App\Services\Tax\Providers\TaxProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class UpdateLocationTaxDataTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_constructs_the_job_correctly()
    {
        $account = Account::factory()->create();
        $company = Company::factory()->create(['account_id' => $account->id]);
        $client = Client::factory()->create(['company_id' => $company->id]);
        $location = Location::factory()->for($client)->for($company)->create([
            'country_id' => 840, // US
        ]);

        $job = new UpdateLocationTaxData($location, $company);

        $this->assertInstanceOf(UpdateLocationTaxData::class, $job);
        $this->assertSame($location->id, $job->location->id);
    }

 

    /** @test */
    public function test_middleware_returns_without_overlapping_instance()
    {
        $account = Account::factory()->create();
        $company = Company::factory()->create(['account_id' => $account->id]);
        $client = Client::factory()->create(['company_id' => $company->id]);
        $location = Location::factory()->for($client)->for($company)->create();

        $job = new UpdateLocationTaxData($location, $company);
        $middleware = $job->middleware();

        $this->assertCount(1, $middleware);
        $this->assertEquals(60, $middleware[0]->releaseAfter);
    }

    /** @test */
    public function test_failed_logs_exception_and_sets_failed_driver_null()
    {
        $account = Account::factory()->create();
        $company = Company::factory()->create(['account_id' => $account->id]);
        $client = Client::factory()->create(['company_id' => $company->id]);
        $location = Location::factory()->for($client)->for($company)->create();

        $job = new UpdateLocationTaxData($location, $company);

        $exception = new \Exception("Test exception");

        $job->failed($exception);

        $this->assertNull(config('queue.failed.driver'));
    }
}

