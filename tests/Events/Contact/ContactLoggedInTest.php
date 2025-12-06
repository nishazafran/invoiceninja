<?php

namespace Tests\Events\Contact;

use App\Events\Contact\ContactLoggedIn;
use App\Models\Company;
use App\Models\ClientContact;
use Tests\TestCase;

class ContactLoggedInTest extends TestCase
{
    public function test_can_be_instantiated_and_broadcast_on()
    {
        $clientContact = new ClientContact(['name' => 'John Doe', 'email' => 'john@example.com']);
        $company = new Company(['name' => 'Acme Inc.']);
        $eventVars = ['ip' => '127.0.0.1'];

        $event = new ContactLoggedIn($clientContact, $company, $eventVars);

        // Assert properties
        $this->assertEquals($clientContact, $event->client_contact);
        $this->assertEquals($company, $event->company);
        $this->assertEquals($eventVars, $event->event_vars);

        // Assert broadcastOn returns empty array
        $this->assertEquals([], $event->broadcastOn());
    }
}

