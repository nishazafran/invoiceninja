<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Policies\CompanyGatewayPolicy;
use App\Models\User;

class CompanyGatewayPolicyTest extends TestCase
{
    public function test_create_returns_true_for_admin_user()
    {
        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn(true);

        $policy = new CompanyGatewayPolicy();

        $this->assertTrue($policy->create($user));
    }

    public function test_create_returns_false_for_non_admin_user()
    {
        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn(false);

        $policy = new CompanyGatewayPolicy();

        $this->assertFalse($policy->create($user));
    }
}

