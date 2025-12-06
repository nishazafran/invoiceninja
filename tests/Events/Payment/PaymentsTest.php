<?php

namespace Tests\Events\Payment;

use Tests\TestCase;
use App\Models\Payment;
use App\Models\Company;
use App\Events\Payment\PaymentCompleted;
use App\Events\Payment\PaymentFailed;
use App\Events\Payment\PaymentWasEmailedAndFailed;
use App\Events\Payment\PaymentWasRefunded;
use App\Events\Payment\PaymentWasVoided;

class PaymentsTest extends TestCase
{
    //PaymentCompleted

    public function test_payment_completed_event_properties()
    {
        $payment = Payment::factory()->make();
        $company = Company::factory()->make();
        $event_vars = ['x' => 1];

        $event = new PaymentCompleted($payment, $company, $event_vars);

        $this->assertSame($payment, $event->payment);
        $this->assertSame($company, $event->company);
        $this->assertSame($event_vars, $event->event_vars);
    }

    //PaymentFailed

    public function test_payment_failed_event_properties()
    {
        $payment = Payment::factory()->make();
        $company = Company::factory()->make();
        $event_vars = ['reason' => 'card declined'];

        $event = new PaymentFailed($payment, $company, $event_vars);

        $this->assertSame($payment, $event->payment);
        $this->assertSame($company, $event->company);
        $this->assertSame($event_vars, $event->event_vars);
    }

    //PaymentWasEmailedAndFailed
    public function test_payment_was_emailed_and_failed_event_properties()
    {
        $payment = Payment::factory()->make();
        $company = Company::factory()->make();
        $errors = "SMTP server unreachable";
        $event_vars = ['email' => 'client@test.com'];

        $event = new PaymentWasEmailedAndFailed($payment, $company, $errors, $event_vars);

        $this->assertSame($payment, $event->payment);
        $this->assertSame($company, $event->company);
        $this->assertEquals($errors, $event->errors);
        $this->assertSame($event_vars, $event->event_vars);
    }

    //PaymentWasRefunded

    public function test_payment_was_refunded_event_properties()
    {
        $payment = Payment::factory()->make();
        $company = Company::factory()->make();
        $refund_amount = 49.99;
        $event_vars = ['method' => 'bank'];

        $event = new PaymentWasRefunded($payment, $refund_amount, $company, $event_vars);

        $this->assertSame($payment, $event->payment);
        $this->assertEquals($refund_amount, $event->refund_amount);
        $this->assertSame($company, $event->company);
        $this->assertSame($event_vars, $event->event_vars);
    }

    //PaymentWasVoided

    public function test_payment_was_voided_event_properties()
    {
        $payment = Payment::factory()->make();
        $company = Company::factory()->make();
        $event_vars = ['user' => 'admin'];

        $event = new PaymentWasVoided($payment, $company, $event_vars);

        $this->assertSame($payment, $event->payment);
        $this->assertSame($company, $event->company);
        $this->assertSame($event_vars, $event->event_vars);
    }
}

