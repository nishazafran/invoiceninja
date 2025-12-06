<?php

namespace Tests\Unit\Transformer;

use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\CompanyToken;
use App\Models\Scheduler;
use App\Models\Account;
use App\Models\CompanyLedger;
use App\Transformers\CompanyTransformer; // replace with actual transformer name
use Mockery;
use PHPUnit\Framework\TestCase;

class CompanyTransformerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testIncludeCompanyUser()
    {
        $companyUser = Mockery::mock(CompanyUser::class);

        $company = Mockery::mock(Company::class)->makePartial();
        $companyUsers = collect([$companyUser]);
        $company->company_users = $companyUsers;

        // Mock auth user
        $user = Mockery::mock();
        $user->id = 1;
        auth()->shouldReceive('user')->andReturn($user);

        $companyUser->shouldReceive('getAttribute')->with('user_id')->andReturn(1);

        $transformer = Mockery::mock(CompanyTransformer::class)->makePartial();
        $transformer->shouldAllowMockingProtectedMethods();
        $transformer->shouldReceive('includeItem')->once()->andReturn('mocked_item');

        $result = $transformer->includeCompanyUser($company);

        $this->assertEquals('mocked_item', $result);
    }

    public function testIncludeTokens()
    {
        $token = Mockery::mock(CompanyToken::class);
        $company = Mockery::mock(Company::class)->makePartial();
        $company->tokens = collect([$token]);

        $transformer = Mockery::mock(CompanyTransformer::class)->makePartial();
        $transformer->shouldAllowMockingProtectedMethods();
        $transformer->shouldReceive('includeCollection')->once()->andReturn('mocked_collection');

        $result = $transformer->includeTokens($company);

        $this->assertEquals('mocked_collection', $result);
    }

    public function testIncludeSchedulers()
    {
        $scheduler = Mockery::mock(Scheduler::class);
        $company = Mockery::mock(Company::class)->makePartial();
        $company->schedulers = collect([$scheduler]);

        $transformer = Mockery::mock(CompanyTransformer::class)->makePartial();
        $transformer->shouldAllowMockingProtectedMethods();
        $transformer->shouldReceive('includeCollection')->once()->andReturn('mocked_collection');

        $result = $transformer->includeSchedulers($company);

        $this->assertEquals('mocked_collection', $result);
    }

    public function testIncludeAccount()
    {
        $account = Mockery::mock(Account::class);
        $company = Mockery::mock(Company::class)->makePartial();
        $company->account = $account;

        $transformer = Mockery::mock(CompanyTransformer::class)->makePartial();
        $transformer->shouldAllowMockingProtectedMethods();
        $transformer->shouldReceive('includeItem')->once()->andReturn('mocked_item');

        $result = $transformer->includeAccount($company);

        $this->assertEquals('mocked_item', $result);
    }

    public function testIncludeLedger()
    {
        $ledger = Mockery::mock(CompanyLedger::class);
        $company = Mockery::mock(Company::class)->makePartial();
        $company->ledger = collect([$ledger]);

        $transformer = Mockery::mock(CompanyTransformer::class)->makePartial();
        $transformer->shouldAllowMockingProtectedMethods();
        $transformer->shouldReceive('includeCollection')->once()->andReturn('mocked_collection');

        $result = $transformer->includeLedger($company);

        $this->assertEquals('mocked_collection', $result);
    }
}

