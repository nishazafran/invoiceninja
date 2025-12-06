<?php

namespace Tests\Events\Subscription;

use Tests\TestCase;
use App\Models\Company;
use App\Models\Subscription;
use App\Events\Subscription\SubscriptionWasCreated;
use App\Events\Subscription\SubscriptionWasArchived;
use App\Events\Subscription\SubscriptionWasDeleted;
use App\Events\Subscription\SubscriptionWasRestored;
use App\Events\Subscription\SubscriptionWasUpdated;

class SubscriptionsTest extends TestCase
{
    private function fakeSubscriptionAndCompany()
    {
        $company = new Company();
        $company->id = 1;
        $company->name = 'Test Company';

        $subscription = new Subscription();
        $subscription->id = 1;
        $subscription->name = 'Test Subscription';

        return [$subscription, $company];
    }

    // ------------------- SubscriptionWasCreated -------------------
    public function test_subscription_was_created_event()
    {
        [$subscription, $company] = $this->fakeSubscriptionAndCompany();
        $eventVars = ['foo' => 'bar'];

        $event = new SubscriptionWasCreated($subscription, $company, $eventVars);

        $this->assertSame($subscription, $event->subscription);
        $this->assertSame($company, $event->company);
        $this->assertSame($eventVars, $event->event_vars);

        $channels = $event->broadcastOn();
        $this->assertIsArray($channels);
        $this->assertCount(0, $channels);
    }

    // ------------------- SubscriptionWasArchived -------------------
    public function test_subscription_was_archived_event()
    {
        [$subscription, $company] = $this->fakeSubscriptionAndCompany();
        $eventVars = ['archived' => true];

        $event = new SubscriptionWasArchived($subscription, $company, $eventVars);

        $this->assertSame($subscription, $event->subscription);
        $this->assertSame($company, $event->company);
        $this->assertSame($eventVars, $event->event_vars);
    }

    // ------------------- SubscriptionWasDeleted -------------------
    public function test_subscription_was_deleted_event()
    {
        [$subscription, $company] = $this->fakeSubscriptionAndCompany();
        $eventVars = ['deleted' => true];

        $event = new SubscriptionWasDeleted($subscription, $company, $eventVars);

        $this->assertSame($subscription, $event->subscription);
        $this->assertSame($company, $event->company);
        $this->assertSame($eventVars, $event->event_vars);
    }

    // ------------------- SubscriptionWasRestored -------------------
    public function test_subscription_was_restored_event()
    {
        [$subscription, $company] = $this->fakeSubscriptionAndCompany();
        $eventVars = ['restored' => true];
        $fromDeleted = true;

        $event = new SubscriptionWasRestored($subscription, $fromDeleted, $company, $eventVars);

        $this->assertSame($subscription, $event->subscription);
        $this->assertSame($fromDeleted, $event->fromDeleted);
        $this->assertSame($company, $event->company);
        $this->assertSame($eventVars, $event->event_vars);
    }

    // ------------------- SubscriptionWasUpdated -------------------
    public function test_subscription_was_updated_event()
    {
        [$subscription, $company] = $this->fakeSubscriptionAndCompany();
        $eventVars = ['updated_field' => 'name'];

        $event = new SubscriptionWasUpdated($subscription, $company, $eventVars);

        $this->assertSame($subscription, $event->subscription);
        $this->assertSame($company, $event->company);
        $this->assertSame($eventVars, $event->event_vars);
    }
}

