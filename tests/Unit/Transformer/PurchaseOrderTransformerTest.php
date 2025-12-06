<?php

namespace Tests\Unit\Transformers;

use App\Models\Activity;
use App\Models\Document;
use App\Models\Expense;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderInvitation;
use App\Models\Vendor;
use App\Transformers\PurchaseOrderTransformer;
use App\Transformers\ActivityTransformer;
use App\Transformers\DocumentTransformer;
use App\Transformers\ExpenseTransformer;
use App\Transformers\PurchaseOrderHistoryTransformer;
use App\Transformers\PurchaseOrderInvitationTransformer;
use App\Transformers\VendorTransformer;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Mockery;
use PHPUnit\Framework\TestCase;

class PurchaseOrderTransformerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function makePurchaseOrderMock(): PurchaseOrder
    {
        $po = Mockery::mock(PurchaseOrder::class)->makePartial();

        $po->id = 1;
        $po->user_id = 2;
        $po->project_id = 3;
        $po->assigned_user_id = 4;
        $po->vendor_id = 5;
        $po->amount = 1000.0;
        $po->balance = 500.0;
        $po->client_id = 6;
        $po->status_id = 1;
        $po->design_id = 7;
        $po->created_at = time();
        $po->updated_at = time();
        $po->deleted_at = null;
        $po->is_deleted = false;
        $po->number = 'PO-001';
        $po->discount = 10.0;
        $po->po_number = 'PO-123';
        $po->date = '2025-12-06';
        $po->last_sent_date = null;
        $po->next_send_date = null;
        $po->reminder1_sent = null;
        $po->reminder2_sent = null;
        $po->reminder3_sent = null;
        $po->reminder_last_sent = null;
        $po->due_date = '2025-12-31';
        $po->terms = 'Terms';
        $po->public_notes = 'Public Notes';
        $po->private_notes = 'Private Notes';
        $po->uses_inclusive_taxes = false;
        $po->tax_name1 = 'Tax1';
        $po->tax_rate1 = 5.0;
        $po->tax_name2 = '';
        $po->tax_rate2 = 0.0;
        $po->tax_name3 = '';
        $po->tax_rate3 = 0.0;
        $po->total_taxes = 50.0;
        $po->is_amount_discount = true;
        $po->footer = 'Footer';
        $po->partial = 100.0;
        $po->partial_due_date = '2025-12-20';
        $po->custom_value1 = 'CV1';
        $po->custom_value2 = 'CV2';
        $po->custom_value3 = 'CV3';
        $po->custom_value4 = 'CV4';
        $po->has_tasks = true;
        $po->has_expenses = true;
        $po->custom_surcharge1 = 10.0;
        $po->custom_surcharge2 = 0.0;
        $po->custom_surcharge3 = 0.0;
        $po->custom_surcharge4 = 0.0;
        $po->custom_surcharge_tax1 = true;
        $po->custom_surcharge_tax2 = false;
        $po->custom_surcharge_tax3 = false;
        $po->custom_surcharge_tax4 = false;
        $po->line_items = ['item1', 'item2'];
        $po->exchange_rate = 1.0;
        $po->paid_to_date = 500.0;
        $po->subscription_id = 10;
        $po->expense_id = 11;
        $po->currency_id = 'USD';
        $po->tax_data = null;
        $po->e_invoice = null;
        $po->location_id = 12;

        // Relations
        $po->activities = [Mockery::mock(Activity::class)];
        $po->invitations = [Mockery::mock(PurchaseOrderInvitation::class)];
        $po->history = [Mockery::mock(Backup::class)];
        $po->documents = [Mockery::mock(Document::class)];
        $po->expense = Mockery::mock(Expense::class);
        $po->vendor = Mockery::mock(Vendor::class);

        return $po;
    }

    /** @test */
    public function transform_returns_correct_array()
    {
        $po = $this->makePurchaseOrderMock();
        $transformer = Mockery::mock(PurchaseOrderTransformer::class)->makePartial();
        $transformer->shouldAllowMockingProtectedMethods();
        $transformer->shouldReceive('encodePrimaryKey')->andReturnUsing(fn($id) => (string)$id);

        $data = $transformer->transform($po);

        $this->assertIsArray($data);
        $this->assertEquals('1', $data['id']);
        $this->assertEquals('2', $data['user_id']);
        $this->assertEquals('3', $data['project_id']);
        $this->assertEquals('4', $data['assigned_user_id']);
        $this->assertEquals('5', $data['vendor_id']);
        $this->assertEquals(1000.0, $data['amount']);
        $this->assertEquals(500.0, $data['balance']);
        $this->assertEquals('6', $data['client_id']);
        $this->assertEquals('PO-001', $data['number']);
        $this->assertEquals('Public Notes', $data['public_notes']);
        $this->assertEquals('Private Notes', $data['private_notes']);
    }

    /** @test */
    public function include_collections_return_collection()
    {
        $po = $this->makePurchaseOrderMock();
        $transformer = new PurchaseOrderTransformer();

        $methods = ['includeActivities', 'includeInvitations', 'includeHistory', 'includeDocuments'];

        foreach ($methods as $method) {
            $resource = $transformer->$method($po);
            $this->assertInstanceOf(Collection::class, $resource);
            $this->assertNotEmpty($resource->getData());
        }
    }

    /** @test */
    public function include_item_returns_item_or_null()
    {
        $po = $this->makePurchaseOrderMock();
        $transformer = new PurchaseOrderTransformer();

        // expense present
        $resource = $transformer->includeExpense($po);
        $this->assertInstanceOf(Item::class, $resource);
        $this->assertNotNull($resource->getData());

        // expense null
        $po->expense = null;
        $resource = $transformer->includeExpense($po);
        $this->assertNull($resource);

        // vendor present
        $po->vendor = Mockery::mock(Vendor::class);
        $resource = $transformer->includeVendor($po);
        $this->assertInstanceOf(Item::class, $resource);

        // vendor null
        $po->vendor = null;
        $resource = $transformer->includeVendor($po);
        $this->assertNull($resource);
    }
}

