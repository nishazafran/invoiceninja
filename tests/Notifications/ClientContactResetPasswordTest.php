<?php

namespace Tests\Notifications;

use PHPUnit\Framework\TestCase;
use App\Notifications\ClientContactResetPassword;
use Closure;

class ClientContactResetPasswordTest extends TestCase
{
    public function test_token_is_set_in_constructor()
    {
        $token = 'sample-token';
        $notification = new ClientContactResetPassword($token);

        $this->assertSame($token, $notification->token);
    }

    public function test_via_returns_empty_array()
    {
        $notification = new ClientContactResetPassword('sample-token');

        $this->assertSame([], $notification->via(null));
    }

    public function test_toMail_can_be_called_without_error()
    {
        $notification = new ClientContactResetPassword('sample-token');

        // It does nothing, but should not throw an error
        $this->assertNull($notification->toMail(null));
    }

    public function test_toMailUsing_sets_static_callback()
    {
        $callback = function () {
            return 'callback called';
        };

        ClientContactResetPassword::toMailUsing($callback);

        $reflection = new \ReflectionClass(ClientContactResetPassword::class);
        $property = $reflection->getProperty('toMailCallback');
        $property->setAccessible(true);

        $this->assertSame($callback, $property->getValue());
    }
}

