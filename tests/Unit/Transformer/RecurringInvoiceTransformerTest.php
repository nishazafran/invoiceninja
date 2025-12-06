<?php

namespace Tests\Unit\Transformers;

use Tests\TestCase;
use App\Models\RecurringInvoice;
use App\Models\Client;
use App\Models\Location;
use App\Models\Backup;
use App\Models\Activity;
use App\Models\Document;
use App\Models\RecurringInvoiceInvitation;
use App\Transformers\RecurringInvoiceTransformer;
use stdClass;
use Illuminate\Support\Collection;

class RecurringInvoiceTransformerTest extends TestCase
{
    protected function makeTransformer(): RecurringInvoiceTransformer
    {
        // Pass null because we are not testing serializer here
        return new RecurringInvoiceTransformer(null);
    }

    /** @test */
    public function it_includes_location_if_present()
    {
        $transformer = $this->makeTransformer();

        $invoice = new RecurringInvoice();
        $invoice->location = new Location();

        $result = $transformer->includeLocation($invoice);

        $this->assertNotNull($result);
    }

    /** @test */
    public function it_returns_null_for_missing_location()
    {
        $transformer = $this->makeTransformer();

        $invoice = new RecurringInvoice();
        $invoice->location = null;

        $result = $transformer->includeLocation($invoice);

        $this->assertNull($result);
    }

    /** @test */
    public function it_includes_history_collection()
    {
        $transformer = $this->makeTransformer();

        $invoice = new RecurringInvoice();
        $invoice->history = collect([new Backup(), new Backup()]);

        $result = $transformer->includeHistory($invoice);

        $this->assertCount(2, $result->getData());
    }

    /** @test */
    public function it_includes_activities_collection()
    {
        $transformer = $this->makeTransformer();

        $invoice = new RecurringInvoice();
        $invoice->activities = collect([new Activity(), new Activity()]);

        $result = $transformer->includeActivities($invoice);

        $this->assertCount(2, $result->getData());
    }

    /** @test */
    public function it_includes_invitations_collection()
    {
        $transformer = $this->makeTransformer();

        $invoice = new RecurringInvoice();
        $invoice->invitations = collect([
            new RecurringInvoiceInvitation(),
            new RecurringInvoiceInvitation()
        ]);

        $result = $transformer->includeInvitations($invoice);

        $this->assertCount(2, $result->getData());
    }

    /** @test */
    public function it_includes_documents_collection()
    {
        $transformer = $this->makeTransformer();

        $invoice = new RecurringInvoice();
        $invoice->documents = collect([new Document(), new Document()]);

        $result = $transformer->includeDocuments($invoice);

        $this->assertCount(2, $result->getData());
    }

    /** @test */
public function it_transforms_invoice_with_recurring_dates()
{
    $transformer = $this->makeTransformer();

    $invoice = $this->getMockBuilder(RecurringInvoice::class)
        ->onlyMethods(['recurringDates'])
        ->getMock();

    $invoice->id = 1;
    $invoice->user_id = 2;
    $invoice->project_id = 3;
    $invoice->assigned_user_id = 4;
    $invoice->client_id = 5;
    $invoice->vendor_id = 6;
    $invoice->status_id = 1;
    $invoice->design_id = 7;
    $invoice->created_at = time();
    $invoice->updated_at = time();
    $invoice->deleted_at = null;
    $invoice->is_deleted = false;
    $invoice->number = 'INV-1001';
    $invoice->amount = 100.50;
    $invoice->balance = 50.25;
    $invoice->discount = 10.0;
    $invoice->line_items = [];
    $invoice->frequency_id = 1;
    $invoice->remaining_cycles = 5;
    $invoice->auto_bill = 'off';
    $invoice->auto_bill_enabled = false;
    $invoice->paid_to_date = 0.0;
    $invoice->client = new Client();

    // Properly mock recurringDates() method
    $invoice->method('recurringDates')->willReturn(['2025-12-01', '2025-12-15']);

    // Simulate request query parameter
    request()->query->set('show_dates', 'true');

    $data = $transformer->transform($invoice);

    $this->assertIsArray($data);
    $this->assertArrayHasKey('recurring_dates', $data);
    $this->assertCount(2, $data['recurring_dates']); // NOW it will pass
    $this->assertEquals('INV-1001', $data['number']);
    $this->assertEquals(100.50, $data['amount']);
}

}

