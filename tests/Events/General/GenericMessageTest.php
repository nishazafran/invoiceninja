<?php

namespace Tests\Events\General;

use Tests\TestCase;
use App\Events\General\GenericMessage;
use Illuminate\Broadcasting\Channel;

class GenericMessageTest extends TestCase
{
    public function test_generic_message_default_channels()
    {
        $event = new GenericMessage('Hello World');

        $this->assertSame('Hello World', $event->message);
        $this->assertNull($event->link);

        // Default channels
        $expected = [
            new Channel(GenericMessage::CHANNEL_HOSTED),
            new Channel(GenericMessage::CHANNEL_SELFHOSTED),
        ];

        $this->assertEquals($expected, $event->broadcastOn());
    }

    public function test_generic_message_custom_channels()
    {
        $event = new GenericMessage('Test', null, [GenericMessage::CHANNEL_SELFHOSTED]);

        $channels = $event->broadcastOn();

        $this->assertCount(1, $channels);
        $this->assertEquals(new Channel(GenericMessage::CHANNEL_SELFHOSTED), $channels[0]);
    }

    public function test_generic_message_no_channels()
    {
        $event = new GenericMessage('Nothing', null, []);

        $this->assertSame([], $event->broadcastOn());
    }

    public function test_generic_message_custom_link()
    {
        $event = new GenericMessage('Msg', 'https://example.com');

        $this->assertSame('Msg', $event->message);
        $this->assertSame('https://example.com', $event->link);
    }
}

