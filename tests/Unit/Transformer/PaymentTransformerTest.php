<?php

namespace Tests\Unit\Transformers;

use App\Models\Payment;
use App\Models\Client;
use App\Models\Credit;
use App\Models\Invoice;
use App\Models\Activity;
use App\Models\Document;
use App\Models\Paymentable;
use App\Models\PaymentType;
use App\Transformers\PaymentTransformer;
use PHPUnit\Framework\TestCase;
use Mockery as m;

class PaymentTransformerTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }

    public function test_include_methods_return_resources()
    {
        $payment = m::mock(Payment::class)->makePartial();

        // Mock relationships
        $payment->activities = collect([m::mock(Activity::class)]);
        $payment->invoices = collect([m::mock(Invoice::class)]);
        $payment->credits = collect([m::mock(Credit::class)]);
        $payment->client = m::mock(Client::class);
        $payment->paymentables = collect([m::mock(Paymentable::class)]);
        $payment->documents = collect([m::mock(Document::class)]);

        $transformer = m::mock(PaymentTransformer::class)->makePartial();
        $transformer->shouldReceive('encodePrimaryKey')->andReturn('encoded');

        $this->assertNotNull($transformer->includeActivities($payment));
        $this->assertNotNull($transformer->includeInvoices($payment));
        $this->assertNotNull($transformer->includeCredits($payment));
        $this->assertNotNull($transformer->includeClient($payment));
        $this->assertNotNull($transformer->includePaymentables($payment));
        $this->assertNotNull($transformer->includeDocuments($payment));
        $this->assertNotNull($transformer->includeType($payment));
    }
public function test_constructor_sets_serializer()
{
    // Pass null to constructor
    $transformer = new \App\Transformers\PaymentTransformer();

    $reflection = new \ReflectionClass($transformer);
    $property = $reflection->getProperty('serializer');
    $property->setAccessible(true);

    $this->assertNull($property->getValue($transformer));

    // Pass a mock serializer
    $mockSerializer = \Mockery::mock(\stdClass::class);
    $transformerWithSerializer = new \App\Transformers\PaymentTransformer($mockSerializer);

    $property2 = (new \ReflectionClass($transformerWithSerializer))->getProperty('serializer');
    $property2->setAccessible(true);

    $this->assertSame($mockSerializer, $property2->getValue($transformerWithSerializer));
}

    public function test_transform_method_returns_array()
    {
        $payment = m::mock(Payment::class)->makePartial();

        // Set attributes for transform
        $payment->id = 1;
        $payment->user_id = 2;
        $payment->assigned_user_id = 3;
        $payment->amount = 100;
        $payment->refunded = 10;
        $payment->applied = 50;
        $payment->transaction_reference = 'TXN123';
        $payment->transaction_id = 4;
        $payment->date = '2025-12-06';
        $payment->is_manual = true;
        $payment->created_at = time();
        $payment->updated_at = time();
        $payment->deleted_at = null;
        $payment->is_deleted = false;
        $payment->type_id = 1;
        $payment->invitation_id = 2;
        $payment->private_notes = 'Private';
        $payment->number = 'PMT-001';
        $payment->custom_value1 = 'C1';
        $payment->custom_value2 = 'C2';
        $payment->custom_value3 = 'C3';
        $payment->custom_value4 = 'C4';
        $payment->client_id = 5;
        $payment->client_contact_id = 6;
        $payment->company_gateway_id = 7;
        $payment->gateway_type_id = 'G1';
        $payment->status_id = 1;
        $payment->project_id = 8;
        $payment->vendor_id = 9;
        $payment->currency_id = 'USD';
        $payment->exchange_rate = 1;
        $payment->exchange_currency_id = 'EUR';

        $transformer = m::mock(PaymentTransformer::class)->makePartial();
        $transformer->shouldReceive('encodePrimaryKey')->andReturn('encoded');

        $result = $transformer->transform($payment);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('amount', $result);
        $this->assertArrayHasKey('refunded', $result);
        $this->assertArrayHasKey('applied', $result);
        $this->assertArrayHasKey('transaction_reference', $result);
        $this->assertArrayHasKey('client_id', $result);
        $this->assertArrayHasKey('currency_id', $result);
    }
}

