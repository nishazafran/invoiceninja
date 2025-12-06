<?php

namespace Tests\Unit\Rules;

use App\Rules\Subscriptions\Steps;
use App\Livewire\BillingPortal\Purchase;
use PHPUnit\Framework\TestCase;

// Mock helper functions inside the namespace to avoid Laravel translation errors
if (!function_exists(__NAMESPACE__ . '\ctrans')) {
    function ctrans(string $string, $replace = [], $locale = null): string
    {
        return $string; // just return the key
    }
}

if (!function_exists(__NAMESPACE__ . '\trans')) {
    function trans(string $string, $replace = [], $locale = null): string
    {
        return $string; // just return the key
    }
}

class StepsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Minimal dependencies for StepService
        Purchase::$dependencies = [
            'AuthStep'    => ['id' => 'auth.login', 'dependencies' => []],
            'PlanStep'    => ['id' => 'billing.plan', 'dependencies' => ['auth.login']],
            'PaymentStep' => ['id' => 'billing.payment', 'dependencies' => ['billing.plan']],
        ];
    }

    /** Test that validate passes with correct steps */
    public function test_validate_passes_when_steps_are_correct()
    {
        $rule = new Steps();
        $failed = false;

        $rule->validate('steps', 'auth.login,billing.plan,billing.payment', function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed); // success path
    }

}

