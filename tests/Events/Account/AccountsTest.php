<?php

namespace Tests\Events;

use App\Events\Account\AccountCreated;
use App\Events\Account\AccountDeleted;
use App\Events\Account\StripeConnectFailure;
use App\Models\Company;
use PHPUnit\Framework\TestCase;

class AccountsTest extends TestCase
{
    public function test_can_instantiate_account_created_and_broadcast()
    {
        $user = (object) ['id' => 1, 'name' => 'John Doe'];
        $company = (object) ['id' => 1, 'name' => 'Acme Inc.'];
        $eventVars = ['key' => 'value'];

        $event = new AccountCreated($user, $company, $eventVars);

        $this->assertEquals($user, $event->user);
        $this->assertEquals($company, $event->company);
        $this->assertEquals($eventVars, $event->event_vars);

        $this->assertEquals([], $event->broadcastOn());
    }

    public function test_can_instantiate_account_deleted_and_broadcast()
    {
        $event = new AccountDeleted('ABC123', 'test@example.com', '127.0.0.1');

        $this->assertEquals('ABC123', $event->account_key);
        $this->assertEquals('test@example.com', $event->email);
        $this->assertEquals('127.0.0.1', $event->ip);

        $this->assertEquals([], $event->broadcastOn());
    }


    public function test_can_instantiate_stripe_connect_failure_and_broadcast()
    {
        $company = new Company(['name' => 'Acme Inc.']);
        $db = 'test_db';

        $event = new StripeConnectFailure($company, $db);

        $this->assertEquals($company, $event->company);
        $this->assertEquals($db, $event->db);

        $this->assertEquals([], $event->broadcastOn());
    }
}

