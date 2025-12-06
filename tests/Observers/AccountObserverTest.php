<?php

namespace Tests\Observers;

use App\Models\Account;
use App\Observers\AccountObserver;
use PHPUnit\Framework\TestCase;

class AccountObserverTest extends TestCase
{
    public function test_observer_methods_are_callable()
    {
        $observer = new AccountObserver();
        $account = $this->createMock(Account::class);

        $observer->created($account);
        $observer->updated($account);
        $observer->deleted($account);
        $observer->restored($account);
        $observer->forceDeleted($account);

        $this->assertTrue(true); // Dummy assertion just to satisfy PHPUnit
    }
}

