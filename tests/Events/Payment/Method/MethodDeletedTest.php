<?php

namespace Tests\Events\Payment\Method;

use Tests\TestCase;
use App\Models\ClientGatewayToken;
use App\Models\Company;
use App\Events\Payment\Methods\MethodDeleted;

class MethodDeletedTest extends TestCase
{
    public function test_method_deleted_event_properties()
    {
        $paymentMethod = new ClientGatewayToken();
        $company = new Company();
        $event_vars = ['key' => 'value'];

        $event = new MethodDeleted($paymentMethod, $company, $event_vars);

        $this->assertSame($paymentMethod, $event->payment_method);
        $this->assertSame($company, $event->company);
        $this->assertSame($event_vars, $event->event_vars);
        $this->assertEquals([], $event->broadcastOn());
    }
}

