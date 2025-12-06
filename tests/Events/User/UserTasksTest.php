<?php

namespace Tests\Events\User;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Events\User\UserWasCreated;
use App\Events\User\UserWasUpdated;
use App\Events\User\UserWasRestored;
use App\Events\User\UserWasDeleted;
use App\Events\User\UserWasArchived;
use App\Events\User\UserLoggedIn;
use App\Events\User\UserLoginFailed;

class UserTasksTest extends TestCase
{
    private function fakeUserAndCompany()
    {
        $company = new Company();
        $company->id = 1;
        $company->name = 'Test Company';

        $user = new User();
        $user->id = 1;
        $user->name = 'Test User';

        $creatingUser = new User();
        $creatingUser->id = 2;
        $creatingUser->name = 'Creator User';

        return [$user, $creatingUser, $company];
    }

    // ------------------- UserWasCreated -------------------
    public function test_user_was_created_event()
    {
        [$user, $creatingUser, $company] = $this->fakeUserAndCompany();
        $eventVars = ['role' => 'admin'];

        $event = new UserWasCreated($user, $creatingUser, $company, $eventVars, true);

        $this->assertSame($user, $event->user);
        $this->assertSame($creatingUser, $event->creating_user);
        $this->assertSame($company, $event->company);
        $this->assertSame($eventVars, $event->event_vars);
        $this->assertTrue($event->is_react);

        // broadcastOn coverage
        $this->assertIsArray($event->broadcastOn());
        $this->assertEmpty($event->broadcastOn());
    }

    // ------------------- UserWasUpdated -------------------
    public function test_user_was_updated_event()
    {
        [$user, $creatingUser, $company] = $this->fakeUserAndCompany();
        $eventVars = ['updated_field' => 'email'];

        $event = new UserWasUpdated($user, $creatingUser, $company, $eventVars);

        $this->assertSame($user, $event->user);
        $this->assertSame($creatingUser, $event->creating_user);
        $this->assertSame($company, $event->company);
        $this->assertSame($eventVars, $event->event_vars);

        // broadcastOn coverage
        $this->assertIsArray($event->broadcastOn());
        $this->assertEmpty($event->broadcastOn());
    }

    // ------------------- UserWasRestored -------------------
    public function test_user_was_restored_event()
    {
        [$user, $creatingUser, $company] = $this->fakeUserAndCompany();
        $eventVars = ['restored' => true];

        $event = new UserWasRestored($user, $creatingUser, $company, $eventVars);

        $this->assertSame($user, $event->user);
        $this->assertSame($creatingUser, $event->creating_user);
        $this->assertSame($company, $event->company);
        $this->assertSame($eventVars, $event->event_vars);

        // broadcastOn coverage
        $this->assertIsArray($event->broadcastOn());
        $this->assertEmpty($event->broadcastOn());
    }

    // ------------------- UserWasDeleted -------------------
    public function test_user_was_deleted_event()
    {
        [$user, $creatingUser, $company] = $this->fakeUserAndCompany();
        $eventVars = ['deleted' => true];

        $event = new UserWasDeleted($user, $creatingUser, $company, $eventVars);

        $this->assertSame($user, $event->user);
        $this->assertSame($creatingUser, $event->creating_user);
        $this->assertSame($company, $event->company);
        $this->assertSame($eventVars, $event->event_vars);

        // broadcastOn coverage
        $this->assertIsArray($event->broadcastOn());
        $this->assertEmpty($event->broadcastOn());
    }

    // ------------------- UserWasArchived -------------------
    public function test_user_was_archived_event()
    {
        [$user, $creatingUser, $company] = $this->fakeUserAndCompany();
        $eventVars = ['archived' => true];

        $event = new UserWasArchived($user, $creatingUser, $company, $eventVars);

        $this->assertSame($user, $event->user);
        $this->assertSame($creatingUser, $event->creating_user);
        $this->assertSame($company, $event->company);
        $this->assertSame($eventVars, $event->event_vars);

        // broadcastOn coverage
        $this->assertIsArray($event->broadcastOn());
        $this->assertEmpty($event->broadcastOn());
    }

    // ------------------- UserLoggedIn -------------------
    public function test_user_logged_in_event()
    {
        [$user, , $company] = $this->fakeUserAndCompany();
        $eventVars = ['login_time' => now()];

        $event = new UserLoggedIn($user, $company, $eventVars);

        $this->assertSame($user, $event->user);
        $this->assertSame($company, $event->company);
        $this->assertSame($eventVars, $event->event_vars);

        // broadcastOn coverage
        $this->assertIsArray($event->broadcastOn());
        $this->assertEmpty($event->broadcastOn());
    }

    // ------------------- UserLoginFailed -------------------
    public function test_user_login_failed_event()
    {
        $email = 'fail@example.com';
        $ip = '127.0.0.1';

        $event = new UserLoginFailed($email, $ip);

        $this->assertSame($email, $event->email);
        $this->assertSame($ip, $event->ip);

        // broadcastOn coverage
        $this->assertIsArray($event->broadcastOn());
        $this->assertEmpty($event->broadcastOn());
    }
}

