<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Policies\TaskStatusPolicy;
use App\Models\User;

class TaskStatusPolicyTest extends TestCase
{
    public function test_create_returns_true_for_admin_user()
    {
        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn(true);
        // hasPermission should not matter because isAdmin is true
        $user->method('hasPermission')->willReturn(false);

        $policy = new TaskStatusPolicy();

        $this->assertTrue($policy->create($user));
    }

    public function test_create_returns_true_for_user_with_create_all_permission()
    {
        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn(false);
        $user->method('hasPermission')->with('create_all')->willReturn(true);

        $policy = new TaskStatusPolicy();

        $this->assertTrue($policy->create($user));
    }

    public function test_create_returns_false_for_non_admin_user_without_permission()
    {
        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn(false);
        $user->method('hasPermission')->with('create_all')->willReturn(false);

        $policy = new TaskStatusPolicy();

        $this->assertFalse($policy->create($user));
    }
}

