<?php

namespace Tests\Unit\Transformers;

use App\Models\Account;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\User;
use App\Transformers\AccountTransformer;
use App\Transformers\CompanyTransformer;
use App\Transformers\CompanyUserTransformer;
use App\Transformers\UserTransformer;
use Tests\TestCase;
use Mockery;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class AccountTransformerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_account_transformer_returns_correct_array()
    {
        $account = Mockery::mock(Account::class)->makePartial();

        $account->id = 1;
        $account->key = 'test_key';
        $account->plan_term = 'monthly';
        $account->plan_started = now();
        $account->plan_paid = now();
        $account->plan_expires = now()->addMonth();
        $account->user_agent = 'phpunit';
        $account->payment_id = 5;
        $account->trial_started = now();
        $account->trial_plan = 'trial';
        $account->plan_price = 99.99;
        $account->num_users = 5;
        $account->utm_source = '';
        $account->utm_medium = '';
        $account->utm_content = '';
        $account->utm_term = '';
        $account->referral_code = '';
        $account->latest_version = '1.0.0';
        $account->updated_at = now()->timestamp;
        $account->deleted_at = null;
        $account->report_errors = true;
        $account->is_scheduler_running = true;
        $account->default_company_id = 1;
        $account->is_migrated = false;
        $account->hosted_client_count = 0;
        $account->hosted_company_count = 0;
        $account->set_react_as_default_ap = false;
        $account->account_sms_verified = true;
        $account->inapp_transaction_id = null;
        $account->e_invoice_quota = 100;
        $account->docuninja_num_users = 1;

        $account->shouldReceive('getPlan')->andReturn('pro');
        $account->shouldReceive('emailsSent')->andReturn(10);
        $account->shouldReceive('getDailyEmailLimit')->andReturn(50);
        $account->shouldReceive('canTrial')->andReturn(true);

        $transformer = new AccountTransformer();

        $result = $transformer->transform($account);

        $this->assertIsArray($result);
        $this->assertEquals('pro', $result['plan']);
        $this->assertEquals('test_key', $result['key']);
        $this->assertEquals(10, $result['emails_sent']);
        $this->assertEquals(50, $result['email_quota']);
    }

    public function test_include_company_users_returns_collection()
    {
        $account = Mockery::mock(Account::class)->makePartial();
        $companyUser1 = Mockery::mock(CompanyUser::class);
        $companyUser2 = Mockery::mock(CompanyUser::class);

        $account->company_users = collect([$companyUser1, $companyUser2]);

        $transformer = new AccountTransformer();
        $resource = $transformer->includeCompanyUsers($account);

        $this->assertInstanceOf(Collection::class, $resource);
        $this->assertEquals(CompanyUserTransformer::class, get_class($resource->getTransformer()));
    }

    public function test_include_default_company_returns_item()
    {
        $account = Mockery::mock(Account::class)->makePartial();
        $company = Mockery::mock(Company::class);
        $account->default_company = $company;

        $transformer = new AccountTransformer();
        $resource = $transformer->includeDefaultCompany($account);

        $this->assertInstanceOf(Item::class, $resource);
        $this->assertEquals(CompanyTransformer::class, get_class($resource->getTransformer()));
    }

    public function test_include_user_returns_item()
    {
        $account = Mockery::mock(Account::class)->makePartial();
        $user = Mockery::mock(User::class);
        $this->be($user); // sets auth()->user() in tests

        $transformer = new AccountTransformer();
        $resource = $transformer->includeUser($account);

        $this->assertInstanceOf(Item::class, $resource);
        $this->assertEquals(UserTransformer::class, get_class($resource->getTransformer()));
    }
}

