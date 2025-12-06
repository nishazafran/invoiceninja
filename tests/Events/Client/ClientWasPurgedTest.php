<?php

namespace Tests\Events\Client;

use App\Events\Client\ClientWasPurged;
use App\Models\User;
use App\Models\Company;
use PHPUnit\Framework\TestCase;

class ClientWasPurgedTest extends TestCase
{

    public function test_can_be_instantiated_and_properties_are_set()
    {
        $user = new User(['name' => 'John Doe', 'email' => 'john@example.com']);
        $company = new Company(['name' => 'Acme Inc.']);
        $purgedClient = 'Client123';
        $eventVars = ['key' => 'value'];

        $event = new ClientWasPurged($purgedClient, $user, $company, $eventVars);

        $this->assertEquals($purgedClient, $event->purged_client);
        $this->assertEquals($user, $event->user);
        $this->assertEquals($company, $event->company);
        $this->assertEquals($eventVars, $event->event_vars);
    }
}

