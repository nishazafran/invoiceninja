<?php

namespace Tests\Unit\Transformers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\CompanyToken;
use App\Models\CompanyUser;
use App\Transformers\UserTransformer;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Mockery;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserTransformerTest extends TestCase
{
    /** @test */
    public function it_transforms_user_with_all_fields()
    {
        $user = new User();
        $user->id = 1;
        $user->first_name = 'John';
        $user->last_name = 'Doe';
        $user->email = 'john@example.com';
        $user->last_login = now();
        $user->created_at = time();
        $user->updated_at = time();
        $user->deleted_at = null;
        $user->is_deleted = false;
        $user->phone = '1234567890';
        $user->password = 'secret';
        $user->verified_phone_number = true;
        $user->user_logged_in_notification = true;
        $user->referral_code = 'ABC123';
        $user->referral_meta = null;

        $transformer = new UserTransformer();
        $data = $transformer->transform($user);

        $this->assertEquals($transformer->encodePrimaryKey(1), $data['id']);
        $this->assertEquals('John', $data['first_name']);
        $this->assertEquals('Doe', $data['last_name']);
        $this->assertEquals('john@example.com', $data['email']);
        $this->assertTrue($data['has_password']);
        $this->assertTrue($data['verified_phone_number']);
        $this->assertTrue($data['user_logged_in_notification']);
        $this->assertEquals('ABC123', $data['referral_code']);
        $this->assertIsObject($data['referral_meta']);
    }

    /** @test */
public function it_includes_related_collections_and_items()
{
    // Mock request header so includeCompanyUser() does not hit database
    $request = Mockery::mock('alias:Illuminate\Support\Facades\Request');
    $request->shouldReceive('header')->with('X-API-TOKEN')->andReturnNull();

    $user = Mockery::mock(User::class)->makePartial();

    // Companies
    $company = new Company();
    $user->companies = [$company];

    // Tokens
    $token = new CompanyToken();
    $user->token = $token;
    $user->tokens = [$token];

    // CompanyUsers relation mock
    $companyUser = new CompanyUser();
    $relationMock = Mockery::mock(HasMany::class);
    $relationMock->shouldReceive('where->first')->andReturn($companyUser);
    $user->shouldReceive('company_users')->andReturn($relationMock);
    $user->company_users = [$companyUser];

    $transformer = new UserTransformer();

    // Assertions
    $this->assertInstanceOf(Collection::class, $transformer->includeCompanies($user));
    $this->assertInstanceOf(Item::class, $transformer->includeToken($user));
    $this->assertInstanceOf(Collection::class, $transformer->includeCompanyTokens($user));
    $this->assertInstanceOf(Item::class, $transformer->includeCompanyUser($user));
    $this->assertInstanceOf(Collection::class, $transformer->includeCompanyUsers($user));
}

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

