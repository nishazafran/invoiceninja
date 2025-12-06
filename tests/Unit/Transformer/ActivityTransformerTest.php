<?php

namespace Tests\Unit\Transformers;

use App\Models\Activity;
use App\Models\Client;
use App\Models\User;
use App\Models\Invoice;
use App\Models\Credit;
use App\Models\Quote;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Task;
use App\Models\Vendor;
use App\Models\VendorContact;
use App\Models\Backup;
use App\Models\PurchaseOrder;
use App\Models\RecurringInvoice;
use App\Transformers\ActivityTransformer;
use Tests\TestCase;
use Mockery;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;

class ActivityTransformerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function getMockTransformer(): ActivityTransformer
    {
        $transformer = Mockery::mock(ActivityTransformer::class)->makePartial();
        $transformer->shouldAllowMockingProtectedMethods();
        $transformer->shouldReceive('encodePrimaryKey')->andReturnUsing(fn($id) => 'hashed_' . $id);

        return $transformer;
    }

    public function test_transform_returns_correct_array()
    {
        $activity = Mockery::mock(Activity::class)->makePartial();
        $activity->id = 1;
        $activity->activity_type_id = 2;
        $activity->client_id = 10;
        $activity->user_id = 5;
        $activity->invoice_id = 20;
        $activity->updated_at = now()->timestamp;
        $activity->created_at = now()->timestamp;
        $activity->is_system = true;
        $activity->notes = 'Test note';
        $activity->ip = '127.0.0.1';

        $transformer = $this->getMockTransformer();

        $data = $transformer->transform($activity);

        $this->assertEquals('hashed_1', $data['id']);
        $this->assertEquals('2', $data['activity_type_id']);
        $this->assertEquals('hashed_10', $data['client_id']);
        $this->assertEquals('hashed_5', $data['user_id']);
        $this->assertEquals('hashed_20', $data['invoice_id']);
        $this->assertTrue($data['is_system']);
        $this->assertEquals('Test note', $data['notes']);
        $this->assertEquals('127.0.0.1', $data['ip']);
    }

    public function test_include_client_returns_transformer_item_or_null()
    {
        $activity = Mockery::mock(Activity::class)->makePartial();

        // null client
        $activity->client = null;
        $transformer = $this->getMockTransformer();
        $this->assertNull($transformer->includeClient($activity));

        // mock client
        $activity->client = Mockery::mock(Client::class);
        $result = $transformer->includeClient($activity);
        $this->assertInstanceOf(Item::class, $result);
    }

    public function test_include_user_returns_transformer_item_or_null()
    {
        $activity = Mockery::mock(Activity::class)->makePartial();

        $activity->user = null;
        $transformer = $this->getMockTransformer();
        $this->assertNull($transformer->includeUser($activity));

        $activity->user = Mockery::mock(User::class);
        $result = $transformer->includeUser($activity);
        $this->assertInstanceOf(Item::class, $result);
    }

    public function test_include_invoice_returns_transformer_item_or_null()
    {
        $activity = Mockery::mock(Activity::class)->makePartial();
        $transformer = $this->getMockTransformer();

        $activity->invoice = null;
        $this->assertNull($transformer->includeInvoice($activity));

        $activity->invoice = Mockery::mock(Invoice::class);
        $result = $transformer->includeInvoice($activity);
        $this->assertInstanceOf(Item::class, $result);
    }

    public function test_include_credit_returns_transformer_item_or_null()
    {
        $activity = Mockery::mock(Activity::class)->makePartial();
        $transformer = $this->getMockTransformer();

        $activity->credit = null;
        $this->assertNull($transformer->includeCredit($activity));

        $activity->credit = Mockery::mock(Credit::class);
        $result = $transformer->includeCredit($activity);
        $this->assertInstanceOf(Item::class, $result);
    }

    public function test_include_quote_returns_transformer_item_or_null()
    {
        $activity = Mockery::mock(Activity::class)->makePartial();
        $transformer = $this->getMockTransformer();

        $activity->quote = null;
        $this->assertNull($transformer->includeQuote($activity));

        $activity->quote = Mockery::mock(Quote::class);
        $result = $transformer->includeQuote($activity);
        $this->assertInstanceOf(Item::class, $result);
    }

    public function test_include_payment_returns_transformer_item_or_null()
    {
        $activity = Mockery::mock(Activity::class)->makePartial();
        $transformer = $this->getMockTransformer();

        $activity->payment = null;
        $this->assertNull($transformer->includePayment($activity));

        $activity->payment = Mockery::mock(Payment::class);
        $result = $transformer->includePayment($activity);
        $this->assertInstanceOf(Item::class, $result);
    }

    public function test_include_expense_returns_transformer_item_or_null()
    {
        $activity = Mockery::mock(Activity::class)->makePartial();
        $transformer = $this->getMockTransformer();

        $activity->expense = null;
        $this->assertNull($transformer->includeExpense($activity));

        $activity->expense = Mockery::mock(Expense::class);
        $result = $transformer->includeExpense($activity);
        $this->assertInstanceOf(Item::class, $result);
    }

    public function test_include_task_returns_transformer_item_or_null()
    {
        $activity = Mockery::mock(Activity::class)->makePartial();
        $transformer = $this->getMockTransformer();

        $activity->task = null;
        $this->assertNull($transformer->includeTask($activity));

        $activity->task = Mockery::mock(Task::class);
        $result = $transformer->includeTask($activity);
        $this->assertInstanceOf(Item::class, $result);
    }

    public function test_include_vendor_and_vendor_contact_returns_item_or_null()
    {
        $activity = Mockery::mock(Activity::class)->makePartial();
        $transformer = $this->getMockTransformer();

        $activity->vendor = null;
        $this->assertNull($transformer->includeVendor($activity));
        $activity->vendor_contact = null;
        $this->assertNull($transformer->includeVendorContact($activity));

        $activity->vendor = Mockery::mock(Vendor::class);
        $this->assertInstanceOf(Item::class, $transformer->includeVendor($activity));

        $activity->vendor_contact = Mockery::mock(VendorContact::class);
        $this->assertInstanceOf(Item::class, $transformer->includeVendorContact($activity));
    }

    public function test_include_recurring_invoice_and_purchase_order_returns_item_or_null()
    {
        $activity = Mockery::mock(Activity::class)->makePartial();
        $transformer = $this->getMockTransformer();

        $activity->recurring_invoice = null;
        $this->assertNull($transformer->includeRecurringInvoice($activity));

        $activity->purchase_order = null;
        $this->assertNull($transformer->includePurchaseOrder($activity));

        $activity->recurring_invoice = Mockery::mock(RecurringInvoice::class);
        $activity->purchase_order = Mockery::mock(PurchaseOrder::class);

        $this->assertInstanceOf(Item::class, $transformer->includeRecurringInvoice($activity));
        $this->assertInstanceOf(Item::class, $transformer->includePurchaseOrder($activity));
    }

    public function test_include_history_returns_item()
    {
        $activity = Mockery::mock(Activity::class)->makePartial();
        $activity->backup = Mockery::mock(Backup::class);

        $transformer = $this->getMockTransformer();
        $result = $transformer->includeHistory($activity);

        $this->assertInstanceOf(Item::class, $result);
    }
    
    
    public function test_include_contact_returns_null_when_no_contact()
{
    $activity = Mockery::mock(Activity::class)->makePartial();
    $activity->contact = null;

    $transformer = new ActivityTransformer();

    $this->assertNull($transformer->includeContact($activity));
}

public function test_include_contact_returns_transformer_item()
{
    $activity = Mockery::mock(Activity::class)->makePartial();
    $activity->contact = Mockery::mock(\App\Models\ClientContact::class);

    $transformer = new ActivityTransformer();

    $result = $transformer->includeContact($activity);

    $this->assertNotNull($result);
    $this->assertInstanceOf(\League\Fractal\Resource\Item::class, $result);
}

}

