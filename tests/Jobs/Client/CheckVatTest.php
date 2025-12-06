<?php

namespace Tests\Jobs\Client;

use Tests\TestCase;
use App\Jobs\Client\CheckVat;
use App\Models\Client;
use App\Models\Company;
use App\Models\Account;
use App\Services\Tax\TaxService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery;

class CheckVatTest extends TestCase
{
    use RefreshDatabase;

    public function test_constructs_the_job_correctly()
    {
        $account = Account::factory()->create();
        $company = Company::factory()->create([
            'account_id' => $account->id,
        ]);

        $client = Client::factory()->create([
            'company_id' => $company->id,
        ]);

        $job = new CheckVat($client, $company);

        $this->assertInstanceOf(CheckVat::class, $job);
    }


    public function test_middleware_contains_without_overlapping()
    {
        $account = Account::factory()->create();
        $company = Company::factory()->create([
            'account_id' => $account->id,
        ]);
        $client = Client::factory()->create([
            'company_id' => $company->id,
        ]);

        $job = new CheckVat($client, $company);

        $middleware = $job->middleware();

        $this->assertNotEmpty($middleware);
        $this->assertEquals('Illuminate\Queue\Middleware\WithoutOverlapping', get_class($middleware[0]));
    }


    public function test_tries_property_is_set_to_1()
    {
        $account = Account::factory()->create();
        $company = Company::factory()->create([
            'account_id' => $account->id,
        ]);
        $client = Client::factory()->create([
            'company_id' => $company->id,
        ]);

        $job = new CheckVat($client, $company);

        $this->assertEquals(1, $job->tries);
    }
}

