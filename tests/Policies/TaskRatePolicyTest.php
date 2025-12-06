<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Policies\TaxRatePolicy; // your policy class
use App\Models\User;

class TaskRatePolicyTest extends TestCase
{
    public function test_create_returns_true_for_admin_user()
    {
        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn(true);

        $policy = new TaxRatePolicy();

        $this->assertTrue($policy->create($user));
    }

    public function test_create_returns_false_for_non_admin_user()
    {
        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn(false);

        $policy = new TaxRatePolicy();

        $this->assertFalse($policy->create($user));
    }
}

