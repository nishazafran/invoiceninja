<?php

namespace Tests\Jobs\Company;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\CompanyToken;
use App\Jobs\Company\CreateCompanyToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Mockery;

class CreateCompanyTokenTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_creates_a_company_token_with_custom_name()
    {
        // Fake related models
        $account = Account::factory()->create(); 
        $company = Company::factory()->create([
        'account_id' => $account->id,              // <-- assign it
    ]);
    $user = User::factory()->create([
        'account_id' => $account->id,              // <-- optional, link user to same account
    ]);

        // Custom name passed to job
        $job = new CreateCompanyToken($company, $user, "Custom API Token");

        $token = $job->handle();

        $this->assertInstanceOf(CompanyToken::class, $token);
        $this->assertEquals($user->id, $token->user_id);
        $this->assertEquals($company->id, $token->company_id);
        $this->assertEquals($account->id, $token->account_id);

        // Name must equal custom one
        $this->assertEquals("Custom API Token", $token->name);

        // Token must be 64 chars
        $this->assertEquals(64, strlen($token->token));

        // Must be system token
        $this->assertTrue($token->is_system);

        // Token exists in DB
        $this->assertDatabaseHas('company_tokens', [
            'id' => $token->id,
            'name' => "Custom API Token",
            'is_system' => true,
        ]);
    }

    /** @test */
    public function test_uses_users_full_name_when_custom_name_is_empty()
    {
    	$account = Account::factory()->create(); 
        $company = Company::factory()->create([
        'account_id' => $account->id,
    ]);

        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'account_id' => $account->id,
        ]);

        $fullName = "John Doe";

        $job = new CreateCompanyToken($company, $user, "");

        $token = $job->handle();

        $this->assertEquals($fullName, $token->name);
        $this->assertEquals($user->id, $token->user_id);
        $this->assertEquals($company->id, $token->company_id);

        $this->assertDatabaseHas('company_tokens', [
            'id' => $token->id,
            'name' => $fullName,
        ]);
    }

    /** @test */
    public function test_constructor_assigns_properties_properly()
    {
    	$account = Account::factory()->create(); 
        $company = Company::factory()->create([
        'account_id' => $account->id,              // <-- assign it
   	 ]);
        $user = User::factory()->create([
        'account_id' => $account->id,
   	 ]);
        $job = new CreateCompanyToken($company, $user, "X");

        $this->assertNotNull($job);
        $this->assertEquals($company, $this->getPrivateProperty($job, 'company'));
        $this->assertEquals($user, $this->getPrivateProperty($job, 'user'));
        $this->assertEquals("X", $this->getPrivateProperty($job, 'custom_token_name'));
    }

    /**
     * Helper to read protected props using reflection
     */
    private function getPrivateProperty($object, string $property)
    {
        $ref = new \ReflectionClass($object);
        $prop = $ref->getProperty($property);
        $prop->setAccessible(true);
        return $prop->getValue($object);
    }
}

