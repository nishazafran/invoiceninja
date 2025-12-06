<?php

namespace Tests\Unit;

use App\Models\User;
use App\Policies\WebhookPolicy;
use PHPUnit\Framework\TestCase;

class WebhookPolicyTest extends TestCase
{
    private WebhookPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new WebhookPolicy();
    }

    /** @test */
    public function create_returns_true_for_admin_user(): void
    {
        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn(true);

        $this->assertTrue($this->policy->create($user));
    }

    /** @test */
    public function create_returns_false_for_non_admin_user(): void
    {
        $user = $this->createMock(User::class);
        $user->method('isAdmin')->willReturn(false);

        $this->assertFalse($this->policy->create($user));
    }
}

