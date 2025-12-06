<?php

namespace Tests\Notifications;

use App\Notifications\Admin\EntitySentNotification;
use Tests\TestCase;
use Mockery;

class EntitySentNotificationTest extends TestCase
{
    private function makeInvitation()
    {
        $client = Mockery::mock();
        $client->shouldReceive('getMergedSettings')->andReturn([]);

        $entity = new \stdClass();
        $entity->client = $client;

        $inv = new \stdClass();
        $inv->contact = new \stdClass();
        $inv->company = new \stdClass();
        $inv->invoice = $entity;   // entity_name = "invoice"

        return $inv;
    }

    /** @test */
    public function via_returns_empty_array_when_no_method_set()
    {
        $inv = $this->makeInvitation();

        $notification = new EntitySentNotification($inv, 'invoice');

        $this->assertSame([], $notification->via(null));
    }

    /** @test */
    public function via_returns_method_array_when_method_is_set()
    {
        $inv = $this->makeInvitation();

        $notification = new EntitySentNotification($inv, 'invoice');
        $notification->method = ['slack'];

        $this->assertSame(['slack'], $notification->via(null));
    }

    /** @test */
    public function to_array_returns_empty_array()
    {
        $inv = $this->makeInvitation();
        $notification = new EntitySentNotification($inv, 'invoice');

        $this->assertSame([], $notification->toArray(null));
    }

    /** @test */
    public function to_mail_executes_without_error()
    {
        $inv = $this->makeInvitation();
        $notification = new EntitySentNotification($inv, 'invoice');

        // this line alone covers the method
        $result = $notification->toMail(null);

        $this->assertNull($result); // empty method returns null
    }
}

