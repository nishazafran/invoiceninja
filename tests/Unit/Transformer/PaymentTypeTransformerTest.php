<?php

namespace Tests\Unit\Transformers;

use App\Models\Payment;
use App\Transformers\PaymentTypeTransformer;
use PHPUnit\Framework\TestCase;
use Mockery as m;

class PaymentTypeTransformerTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }

    public function test_transform_returns_name()
    {
        // Mock Payment
        $payment = m::mock(Payment::class);
        $payment->shouldReceive('translatedType')
                ->once()
                ->andReturn('Credit Card');

        $transformer = new PaymentTypeTransformer();

        $result = $transformer->transform($payment);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('name', $result);
        $this->assertSame('Credit Card', $result['name']);
    }
}

