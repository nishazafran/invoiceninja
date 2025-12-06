<?php

namespace Tests\Jobs\Account;

use Tests\TestCase;
use App\Jobs\Account\CreateAccount;
use App\Models\Account;
use App\Models\User;
use App\Models\Company;
use Mockery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use App\Jobs\Company\CreateCompany;
use App\Jobs\User\CreateUser;
use App\Jobs\Company\CreateCompanyToken;
use App\Jobs\Company\CreateCompanyPaymentTerms;
use App\Jobs\Company\CreateCompanyTaskStatuses;

class CreateAccountTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_creates_an_account_successfully()
    {
        Queue::fake(); // prevent dispatching jobs

        $request = [
            'email' => 'test@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ];

        // Real models to satisfy Authenticatable requirement
        $userMock = User::factory()->make();
        $companyMock = Company::factory()->make();
        $companyMock->id = 1;
        $companyMock->settings = [];

        // Mock dependent jobs to return real models
        Mockery::mock('overload:' . CreateCompany::class)
            ->shouldReceive('handle')
            ->andReturn($companyMock);

        Mockery::mock('overload:' . CreateUser::class)
            ->shouldReceive('handle')
            ->andReturn($userMock);

        Mockery::mock('overload:' . CreateCompanyToken::class)
            ->shouldReceive('handle')
            ->andReturnTrue();

        Mockery::mock('overload:' . CreateCompanyPaymentTerms::class)
            ->shouldReceive('handle')
            ->andReturnTrue();

        Mockery::mock('overload:' . CreateCompanyTaskStatuses::class)
            ->shouldReceive('handle')
            ->andReturnTrue();

        $job = new CreateAccount($request, '127.0.0.1');
        $account = $job->handle();

        $this->assertInstanceOf(Account::class, $account);
        //$this->assertEquals('test@example.com', $account->email);
        $this->assertEquals(1, $account->default_company_id);
    }
}

