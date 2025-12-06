<?php

namespace Tests\Notifications\Admin;

use PHPUnit\Framework\TestCase;
use App\Notifications\NewAccountCreated;
use Illuminate\Notifications\Messages\SlackMessage;

class NewAccountTest extends TestCase
{
    public function test_properties_are_set_correctly()
    {
        $user = $this->createMock(\stdClass::class);
        $company = $this->createMock(\stdClass::class);

        $notification = new NewAccountCreated($user, $company, true);

        $reflection = new \ReflectionClass($notification);

        $userProp = $reflection->getProperty('user');
        $userProp->setAccessible(true);
        $this->assertSame($user, $userProp->getValue($notification));

        $companyProp = $reflection->getProperty('company');
        $companyProp->setAccessible(true);
        $this->assertSame($company, $companyProp->getValue($notification));

        $this->assertTrue($notification->is_system);
    }

    public function test_via_returns_slack_array()
    {
        $notification = new NewAccountCreated(new \stdClass(), new \stdClass());
        $this->assertEquals(['slack'], $notification->via(null));
    }

    public function test_toArray_returns_array()
    {
        $notification = new NewAccountCreated(new \stdClass(), new \stdClass());
        $this->assertIsArray($notification->toArray(null));
    }

    public function test_toMail_can_be_called()
    {
        $notification = new NewAccountCreated(new \stdClass(), new \stdClass());
        $notification->toMail(null);
        $this->assertTrue(true);
    }

 

}

