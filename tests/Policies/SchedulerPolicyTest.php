<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Policies\SchedulerPolicy;
use App\Models\User;

class SchedulerPolicyTest extends TestCase
{
    public function test_create_returns_true_for_admin_user()
    {
        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn(true);

        $policy = new SchedulerPolicy();

        $this->assertTrue($policy->create($user));
    }

    public function test_create_returns_false_for_non_admin_user()
    {
        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn(false);

        $policy = new SchedulerPolicy();

        $this->assertFalse($policy->create($user));
    }
}

