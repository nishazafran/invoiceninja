<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Policies\DocumentPolicy;
use App\Models\User;

class DocumentPolicyTest extends TestCase
{
    public function test_create_returns_true_for_admin_user()
    {
        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn(true);

        $policy = new DocumentPolicy();

        $this->assertTrue($policy->create($user));
    }

    public function test_create_returns_true_for_user_with_create_all_permission()
    {
        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn(false);
        $user->method('hasPermission')->with('create_all')->willReturn(true);

        $policy = new DocumentPolicy();

        $this->assertTrue($policy->create($user));
    }

    public function test_create_returns_false_for_non_admin_without_permission()
    {
        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn(false);
        $user->method('hasPermission')->with('create_all')->willReturn(false);

        $policy = new DocumentPolicy();

        $this->assertFalse($policy->create($user));
    }
}

