<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Policies\CompanyPolicy;
use App\Models\User;
use stdClass;

class CompanyPolicyTest extends TestCase
{
    public function test_create_returns_true_for_admin()
    {
        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn(true);
        $user->method('hasPermission')->willReturn(false);

        $policy = new CompanyPolicy();
        $this->assertTrue($policy->create($user));
    }

    public function test_create_returns_true_for_user_with_create_company_permission()
    {
        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn(false);
        $user->method('hasPermission')->willReturnMap([
            ['create_company', true],
            ['create_all', false],
        ]);

        $policy = new CompanyPolicy();
        $this->assertTrue($policy->create($user));
    }

    public function test_create_returns_true_for_user_with_create_all_permission()
    {
        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn(false);
        $user->method('hasPermission')->willReturnMap([
            ['create_company', false],
            ['create_all', true],
        ]);

        $policy = new CompanyPolicy();
        $this->assertTrue($policy->create($user));
    }

    public function test_create_returns_false_for_user_without_permissions()
    {
        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn(false);
        $user->method('hasPermission')->willReturnMap([
            ['create_company', false],
            ['create_all', false],
        ]);

        $policy = new CompanyPolicy();
        $this->assertFalse($policy->create($user));
    }

    public function test_view_permissions()
    {
        $entity = new stdClass();
        $entity->id = 10;

        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn(true);
        $user->method('companyId')->willReturn(10);
        $user->method('hasPermission')->willReturn(false);
        $user->method('owns')->willReturn(false);

        $policy = new CompanyPolicy();
        $this->assertTrue($policy->view($user, $entity));

        // Non-admin with proper permission
        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn(false);
        $user->method('companyId')->willReturn(10);
        $user->method('hasPermission')->willReturnMap([
            ['view_stdclass', true],
        ]);
        $user->method('owns')->willReturn(false);

        $this->assertTrue($policy->view($user, $entity));

        // User owns entity
        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn(false);
        $user->method('companyId')->willReturn(1);
        $user->method('hasPermission')->willReturn(false);
        $user->method('owns')->willReturn(true);

        $this->assertTrue($policy->view($user, $entity));

        // User same company
        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn(false);
        $user->method('companyId')->willReturn(10);
        $user->method('hasPermission')->willReturn(false);
        $user->method('owns')->willReturn(false);

        $this->assertTrue($policy->view($user, $entity));

        // No access
        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn(false);
        $user->method('companyId')->willReturn(1);
        $user->method('hasPermission')->willReturn(false);
        $user->method('owns')->willReturn(false);

        $this->assertFalse($policy->view($user, $entity));
    }

    public function test_edit_permissions()
    {
        $entity = new stdClass();
        $entity->id = 10;

        // Admin user
        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn(true);
        $user->method('companyId')->willReturn(10);
        $user->method('hasPermission')->willReturn(false);
        $user->method('owns')->willReturn(false);

        $policy = new CompanyPolicy();
        $this->assertTrue($policy->edit($user, $entity));

        // User with edit permission
        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn(false);
        $user->method('companyId')->willReturn(10);
        $user->method('hasPermission')->willReturnMap([
            ['edit_stdclass', true],
        ]);
        $user->method('owns')->willReturn(false);

        $this->assertTrue($policy->edit($user, $entity));

        // User owns entity
        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn(false);
        $user->method('companyId')->willReturn(1);
        $user->method('hasPermission')->willReturn(false);
        $user->method('owns')->willReturn(true);

        $this->assertTrue($policy->edit($user, $entity));

        // No access
        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn(false);
        $user->method('companyId')->willReturn(1);
        $user->method('hasPermission')->willReturn(false);
        $user->method('owns')->willReturn(false);

        $this->assertFalse($policy->edit($user, $entity));
    }
}

