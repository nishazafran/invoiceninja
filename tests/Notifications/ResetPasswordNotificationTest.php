<?php

namespace Tests\Notifications;

use PHPUnit\Framework\TestCase;
use App\Notifications\ResetPasswordNotification;

class ResetPasswordNotificationTest extends TestCase
{
    public function test_token_is_set_correctly()
    {
        $token = '123456';
        $notification = new ResetPasswordNotification($token);

        $this->assertEquals($token, $notification->token);
    }

    public function test_via_returns_array()
    {
        $notification = new ResetPasswordNotification('token');
        $this->assertIsArray($notification->via(null));
    }

    public function test_toArray_returns_array()
    {
        $notification = new ResetPasswordNotification('token');
        $this->assertIsArray($notification->toArray(null));
    }

    public function test_toMail_is_callable()
    {
        $notification = new ResetPasswordNotification('token');
        // Just ensure it does not throw an error
        $this->assertNull($notification->toMail(null));
    }
}

