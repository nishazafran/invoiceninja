<?php

namespace Tests\Events\PurchaseOrder;

use Tests\TestCase;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderInvitation;
use App\Models\Company;
use App\Models\VendorContact;

use App\Events\PurchaseOrder\PurchaseOrderWasViewed;
use App\Events\PurchaseOrder\PurchaseOrderWasRestored;
use App\Events\PurchaseOrder\PurchaseOrderWasUpdated;
use App\Events\PurchaseOrder\PurchaseOrderWasEmailed;
use App\Events\PurchaseOrder\PurchaseOrderWasDeleted;
use App\Events\PurchaseOrder\PurchaseOrderWasCreated;
use App\Events\PurchaseOrder\PurchaseOrderWasAccepted;
use App\Events\PurchaseOrder\PurchaseOrderWasArchived;

class PurchaseOrdersTest extends TestCase
{
    private function fakePurchaseOrder()
    {
        return new PurchaseOrder();
    }

    private function fakeInvitation()
    {
        return new PurchaseOrderInvitation();
    }

    private function fakeCompany()
    {
        return new Company();
    }

    private function fakeContact()
    {
        return new VendorContact();
    }

    private function fakeEventVars()
    {
        return ['key' => 'value'];
    }

    // ------------------- PurchaseOrderWasViewed.php -------------------
    public function test_purchase_order_was_viewed()
    {
        $invitation = $this->fakeInvitation();
        $company = $this->fakeCompany();
        $vars = $this->fakeEventVars();

        $event = new PurchaseOrderWasViewed($invitation, $company, $vars);

        $this->assertSame($invitation, $event->invitation);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }

    // ------------------- PurchaseOrderWasRestored.php -------------------
    public function test_purchase_order_was_restored()
    {
        $po = $this->fakePurchaseOrder();
        $company = $this->fakeCompany();
        $vars = $this->fakeEventVars();
        $fromDeleted = true;

        $event = new PurchaseOrderWasRestored($po, $fromDeleted, $company, $vars);

        $this->assertSame($po, $event->purchase_order);
        $this->assertTrue($event->fromDeleted);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }

    // ------------------- PurchaseOrderWasUpdated.php -------------------
    public function test_purchase_order_was_updated()
    {
        $po = $this->fakePurchaseOrder();
        $company = $this->fakeCompany();
        $vars = $this->fakeEventVars();

        $event = new PurchaseOrderWasUpdated($po, $company, $vars);

        $this->assertSame($po, $event->purchase_order);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }

    // ------------------- PurchaseOrderWasEmailed.php -------------------
    public function test_purchase_order_was_emailed()
    {
        $invitation = $this->fakeInvitation();
        $company = $this->fakeCompany();
        $vars = $this->fakeEventVars();

        $event = new PurchaseOrderWasEmailed($invitation, $company, $vars);

        $this->assertSame($invitation, $event->invitation);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }

    // ------------------- PurchaseOrderWasDeleted.php -------------------
    public function test_purchase_order_was_deleted()
    {
        $po = $this->fakePurchaseOrder();
        $company = $this->fakeCompany();
        $vars = $this->fakeEventVars();

        $event = new PurchaseOrderWasDeleted($po, $company, $vars);

        $this->assertSame($po, $event->purchase_order);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }

    // ------------------- PurchaseOrderWasCreated.php -------------------
    public function test_purchase_order_was_created()
    {
        $po = $this->fakePurchaseOrder();
        $company = $this->fakeCompany();
        $vars = $this->fakeEventVars();

        $event = new PurchaseOrderWasCreated($po, $company, $vars);

        $this->assertSame($po, $event->purchase_order);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }

    // ------------------- PurchaseOrderWasAccepted.php -------------------
    public function test_purchase_order_was_accepted()
    {
        $po = $this->fakePurchaseOrder();
        $contact = $this->fakeContact();
        $company = $this->fakeCompany();
        $vars = $this->fakeEventVars();

        $event = new PurchaseOrderWasAccepted($po, $contact, $company, $vars);

        $this->assertSame($po, $event->purchase_order);
        $this->assertSame($contact, $event->contact);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }

    // ------------------- PurchaseOrderWasArchived.php -------------------
    public function test_purchase_order_was_archived()
    {
        $po = $this->fakePurchaseOrder();
        $company = $this->fakeCompany();
        $vars = $this->fakeEventVars();

        $event = new PurchaseOrderWasArchived($po, $company, $vars);

        $this->assertSame($po, $event->purchase_order);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }
}

