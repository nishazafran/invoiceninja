<?php

namespace Tests\Unit\Policies;

use App\Models\CompanyUser;
use App\Models\User;
use App\Policies\UserPolicy;
use Mockery;
use PHPUnit\Framework\TestCase;

class UserPolicyTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_create_returns_true_if_user_is_admin()
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('isAdmin')->once()->andReturn(true);

        $policy = new UserPolicy();

        $this->assertTrue($policy->create($user));
    }

    public function test_create_returns_true_if_user_has_create_user_permission()
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('isAdmin')->once()->andReturn(false);
        $user->shouldReceive('hasPermission')->with('create_user')->once()->andReturn(true);
        $user->shouldReceive('hasPermission')->with('create_all')->never();

        $policy = new UserPolicy();

        $this->assertTrue($policy->create($user));
    }

    public function test_create_returns_false_if_user_has_no_permissions()
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('isAdmin')->once()->andReturn(false);
        $user->shouldReceive('hasPermission')->with('create_user')->once()->andReturn(false);
        $user->shouldReceive('hasPermission')->with('create_all')->once()->andReturn(false);

        $policy = new UserPolicy();

        $this->assertFalse($policy->create($user));
    }

    public function test_edit_returns_true_if_user_is_admin_and_has_company_user()
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->id = 1; // Set ID so whereUserId($user->id) works
        $user->shouldReceive('isAdmin')->once()->andReturn(true);

        // Mock the static CompanyUser call
        $companyUserMock = Mockery::mock('alias:App\Models\CompanyUser');
        $queryMock = Mockery::mock();
        $queryMock->shouldReceive('AuthCompany')->once()->andReturnSelf();
        $queryMock->shouldReceive('first')->once()->andReturn(true);

        $companyUserMock->shouldReceive('whereUserId')->with($user->id)->once()->andReturn($queryMock);

        $policy = new UserPolicy();

        $this->assertTrue($policy->edit($user, new User()));
    }

   public function test_edit_returns_false_if_user_is_not_admin() 
   {
    	$user = Mockery::mock(User::class)->makePartial();
    	$user->id = 1;
    	$user->shouldReceive('isAdmin')->once()->andReturn(false);
     
   	$policy = new UserPolicy();
   	$this->assertFalse($policy->edit($user, new User()));
   }
    
    public function test_edit_returns_false_if_user_is_admin_but_no_company_user_found()
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('isAdmin')->once()->andReturn(true);

        $companyUserMock = Mockery::mock('alias:App\Models\CompanyUser');
        $queryMock = Mockery::mock();
        $queryMock->shouldReceive('AuthCompany')->once()->andReturnSelf();
        $queryMock->shouldReceive('first')->once()->andReturn(null); // simulate no company_user

        $companyUserMock->shouldReceive('whereUserId')->with($user->id)->once()->andReturn($queryMock);

        $policy = new UserPolicy();

        $this->assertFalse($policy->edit($user, new User()));
    }
}

