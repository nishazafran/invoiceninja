<?php

namespace Tests\Observers;

use PHPUnit\Framework\TestCase;
use App\Observers\CompanyTokenObserver;
use App\Models\CompanyToken;

class CompanyTokenObserverTest extends TestCase
{
    protected CompanyTokenObserver $observer;
    protected CompanyToken $companyToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->observer = new CompanyTokenObserver();

        // Create a mock of CompanyToken
        $this->companyToken = $this->createMock(CompanyToken::class);
    }

    public function test_created_event()
    {
        // Call created method
        $this->observer->created($this->companyToken);

        // Since method is empty, just assert true to satisfy PHPUnit
        $this->assertTrue(true);
    }

    public function test_updated_event()
    {
        $this->observer->updated($this->companyToken);
        $this->assertTrue(true);
    }

    public function test_deleted_event()
    {
        $this->observer->deleted($this->companyToken);
        $this->assertTrue(true);
    }

    public function test_restored_event()
    {
        $this->observer->restored($this->companyToken);
        $this->assertTrue(true);
    }

    public function test_force_deleted_event()
    {
        $this->observer->forceDeleted($this->companyToken);
        $this->assertTrue(true);
    }
}

