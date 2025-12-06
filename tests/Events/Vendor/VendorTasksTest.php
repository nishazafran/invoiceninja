<?php

namespace Tests\Events\Vendor;

use Tests\TestCase;
use App\Models\Company;
use App\Models\Vendor;
use App\Models\VendorContact;
use App\Events\Vendor\VendorWasCreated;
use App\Events\Vendor\VendorWasArchived;
use App\Events\Vendor\VendorWasDeleted;
use App\Events\Vendor\VendorWasMerged;
use App\Events\Vendor\VendorWasRestored;
use App\Events\Vendor\VendorWasUpdated;
use App\Events\Vendor\VendorContactLoggedIn;

class VendorTasksTest extends TestCase
{
    private function fakeVendorAndCompany()
    {
        $company = new Company();
        $company->id = 1;
        $company->name = 'Test Company';

        $vendor = new Vendor();
        $vendor->id = 1;
        $vendor->name = 'Test Vendor';

        return [$vendor, $company];
    }

    // ------------------- VendorWasCreated -------------------
    public function test_vendor_was_created_event()
    {
        [$vendor, $company] = $this->fakeVendorAndCompany();
        $eventVars = ['type' => 'new'];

        $event = new VendorWasCreated($vendor, $company, $eventVars);

        $this->assertSame($vendor, $event->vendor);
        $this->assertSame($company, $event->company);
        $this->assertSame($eventVars, $event->event_vars);
    }

    // ------------------- VendorWasArchived -------------------
    public function test_vendor_was_archived_event()
    {
        [$vendor, $company] = $this->fakeVendorAndCompany();
        $eventVars = ['archived' => true];

        $event = new VendorWasArchived($vendor, $company, $eventVars);

        $this->assertSame($vendor, $event->vendor);
        $this->assertSame($company, $event->company);
        $this->assertSame($eventVars, $event->event_vars);
    }

    // ------------------- VendorWasDeleted -------------------
    public function test_vendor_was_deleted_event()
    {
        [$vendor, $company] = $this->fakeVendorAndCompany();
        $eventVars = ['deleted' => true];

        $event = new VendorWasDeleted($vendor, $company, $eventVars);

        $this->assertSame($vendor, $event->vendor);
        $this->assertSame($company, $event->company);
        $this->assertSame($eventVars, $event->event_vars);
    }

    // ------------------- VendorWasMerged -------------------
    public function test_vendor_was_merged_event()
    {
        [$vendor, $company] = $this->fakeVendorAndCompany();
        $mergeableVendor = 'VendorToMerge';
        $eventVars = ['merged' => true];

        $event = new VendorWasMerged($mergeableVendor, $vendor, $company, $eventVars);

        $this->assertSame($mergeableVendor, $event->mergeable_vendor);
        $this->assertSame($vendor, $event->vendor);
        $this->assertSame($company, $event->company);
        $this->assertSame($eventVars, $event->event_vars);
    }

    // ------------------- VendorWasRestored -------------------
    public function test_vendor_was_restored_event()
    {
        [$vendor, $company] = $this->fakeVendorAndCompany();
        $fromDeleted = true;
        $eventVars = ['restored' => true];

        $event = new VendorWasRestored($vendor, $fromDeleted, $company, $eventVars);

        $this->assertSame($vendor, $event->vendor);
        $this->assertSame($fromDeleted, $event->fromDeleted);
        $this->assertSame($company, $event->company);
        $this->assertSame($eventVars, $event->event_vars);
    }

    // ------------------- VendorWasUpdated -------------------
    public function test_vendor_was_updated_event()
    {
        [$vendor, $company] = $this->fakeVendorAndCompany();
        $eventVars = ['updated_field' => 'address'];

        $event = new VendorWasUpdated($vendor, $company, $eventVars);

        $this->assertSame($vendor, $event->vendor);
        $this->assertSame($company, $event->company);
        $this->assertSame($eventVars, $event->event_vars);
    }

    // ------------------- VendorContactLoggedIn -------------------
    public function test_vendor_contact_logged_in_event()
    {
        [$vendor, $company] = $this->fakeVendorAndCompany();

        $contact = new VendorContact();
        $contact->id = 1;
        $contact->name = 'Test Contact';

        $eventVars = ['login_time' => now()];

        $event = new VendorContactLoggedIn($contact, $company, $eventVars);

        $this->assertSame($contact, $event->contact);
        $this->assertSame($company, $event->company);
        $this->assertSame($eventVars, $event->event_vars);

        // broadcastOn coverage
        $this->assertIsArray($event->broadcastOn());
        $this->assertEmpty($event->broadcastOn());
    }
}

