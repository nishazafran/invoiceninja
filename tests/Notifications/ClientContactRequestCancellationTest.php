<?php

namespace Tests\Notifications;

use PHPUnit\Framework\TestCase;
use App\Notifications\ClientContactRequestCancellation;
use Illuminate\Notifications\Messages\SlackMessage;

class ClientContactRequestCancellationTest extends TestCase
{
    public function test_properties_are_set_correctly()
    {
        $recurring_invoice = new class { public $number = 'INV-123'; };

        $client = new class { 
            public function present() { return $this; }
            public function name() { return 'Acme Corp'; }
        };

        $client_contact = new class($client) {
            public $client;
            public function __construct($client) { $this->client = $client; }
            public function present() { return $this; }
            public function name() { return 'John Doe'; }
        };

         $notification = new ClientContactRequestCancellation($recurring_invoice, $client_contact);

        $reflection = new \ReflectionClass($notification);

        $property = $reflection->getProperty('recurring_invoice');
        $property->setAccessible(true);
        $this->assertSame($recurring_invoice, $property->getValue($notification));

        $property = $reflection->getProperty('client_contact');
        $property->setAccessible(true);
        $this->assertSame($client_contact, $property->getValue($notification));
    }

    public function test_via_returns_slack_array()
    {
        $notification = new ClientContactRequestCancellation(
            new class { public $number = 'INV-123'; },
            new class {
                public $client;
                public function __construct() { $this->client = new class {
                    public function present() { return $this; }
                    public function name() { return 'Acme Corp'; }
                }; }
                public function present() { return $this; }
                public function name() { return 'John Doe'; }
            }
        );

        $this->assertEquals(['slack'], $notification->via(null));
    }

    public function test_toSlack_returns_slack_message()
    {
        $recurring_invoice = new class { public $number = 'INV-123'; };

        $client = new class {
            public function present() { return $this; }
            public function name() { return 'Acme Corp'; }
        };

        $client_contact = new class($client) {
            public $client;
            public function __construct($client) { $this->client = $client; }
            public function present() { return $this; }
            public function name() { return 'John Doe'; }
        };

        $notification = new ClientContactRequestCancellation($recurring_invoice, $client_contact);

        $slackMessage = $notification->toSlack(null);

        $this->assertInstanceOf(SlackMessage::class, $slackMessage);
        $this->assertStringContainsString('John Doe', $slackMessage->content);
        $this->assertStringContainsString('Acme Corp', $slackMessage->content);
        $this->assertStringContainsString('INV-123', $slackMessage->content);
    }
    
    
    
    
    
    public function test_toMail_and_toArray_methods()
{
    $recurring_invoice = new class { public $number = 'INV-123'; };

    $client = new class {
        public function present() { return $this; }
        public function name() { return 'Acme Corp'; }
    };

    $client_contact = new class($client) {
        public $client;
        public function __construct($client) { $this->client = $client; }
        public function present() { return $this; }
        public function name() { return 'John Doe'; }
    };

    $notification = new ClientContactRequestCancellation($recurring_invoice, $client_contact);

    // Call toMail (even if it does nothing)
    $notification->toMail(null);

    // Call toArray
    $result = $notification->toArray(null);
    $this->assertIsArray($result);
}

public function test_toMailUsing_sets_callback()
{
    $callback = function () {
        return 'dummy';
    };

    // Call the static method
    ClientContactRequestCancellation::toMailUsing($callback);

    // Assert the callback was set
    $this->assertSame($callback, ClientContactRequestCancellation::$toMailCallback);
}


}

