<?php

namespace Tests\Unit\Transformers;

use Tests\TestCase;
use App\Models\RecurringQuote;
use App\Models\RecurringQuoteInvitation;
use App\Models\Backup;
use App\Models\Activity;
use App\Models\Document;
use App\Transformers\RecurringQuoteTransformer;

class RecurringQuoteTransformerTest extends TestCase
{
    /** @test */
    public function it_includes_history_activities_invitations_documents()
    {
        $quote = $this->getMockBuilder(RecurringQuote::class)
            ->onlyMethods(['recurringDates'])
            ->getMock();

        // Setup collections
        $quote->history = [new Backup()];
        $quote->activities = [new Activity()];
        $quote->invitations = [new RecurringQuoteInvitation()];
        $quote->documents = [new Document()];

        $transformer = $this->getMockBuilder(RecurringQuoteTransformer::class)
            ->onlyMethods(['includeCollection'])
            ->getMock();

        // make includeCollection just return true for testing
        $transformer->method('includeCollection')->willReturn(true);

        $this->assertTrue($transformer->includeHistory($quote));
        $this->assertTrue($transformer->includeActivities($quote));
        $this->assertTrue($transformer->includeInvitations($quote));
        $this->assertTrue($transformer->includeDocuments($quote));
    }

    /** @test */
    public function it_transforms_quote_with_all_fields()
    {
        $quote = $this->getMockBuilder(RecurringQuote::class)
            ->onlyMethods(['recurringDates'])
            ->getMock();

        $quote->id = 1;
        $quote->user_id = 2;
        $quote->project_id = 3;
        $quote->assigned_user_id = 4;
        $quote->amount = 100;
        $quote->balance = 50;
        $quote->client_id = 5;
        $quote->vendor_id = 6;
        $quote->status_id = 1;
        $quote->design_id = 7;
        $quote->created_at = time();
        $quote->updated_at = time();
        $quote->deleted_at = null;
        $quote->is_deleted = false;
        $quote->number = 'Q-100';
        $quote->discount = 10;
        $quote->po_number = 'PO-123';
        $quote->date = '2025-12-06';
        $quote->last_sent_date = '2025-12-07';
        $quote->next_send_date = '2025-12-08';
        $quote->due_date = '2025-12-15';
        $quote->terms = 'Net 30';
        $quote->public_notes = 'Public Note';
        $quote->private_notes = 'Private Note';
        $quote->uses_inclusive_taxes = true;
        $quote->tax_name1 = 'Tax1';
        $quote->tax_rate1 = 5;
        $quote->tax_name2 = null;
        $quote->tax_rate2 = 0;
        $quote->tax_name3 = null;
        $quote->tax_rate3 = 0;
        $quote->total_taxes = 5;
        $quote->is_amount_discount = false;
        $quote->footer = 'Footer';
        $quote->partial = 0;
        $quote->partial_due_date = null;
        $quote->custom_value1 = null;
        $quote->custom_value2 = null;
        $quote->custom_value3 = null;
        $quote->custom_value4 = null;
        $quote->has_tasks = false;
        $quote->has_expenses = false;
        $quote->custom_surcharge1 = 0;
        $quote->custom_surcharge2 = 0;
        $quote->custom_surcharge3 = 0;
        $quote->custom_surcharge4 = 0;
        $quote->exchange_rate = 1;
        $quote->custom_surcharge_tax1 = false;
        $quote->custom_surcharge_tax2 = false;
        $quote->custom_surcharge_tax3 = false;
        $quote->custom_surcharge_tax4 = false;
        $quote->line_items = [];
        $quote->frequency_id = 1;
        $quote->remaining_cycles = 5;
        $quote->auto_bill = 'off';
        $quote->auto_bill_enabled = false;
        $quote->due_date_days = null;
        $quote->paid_to_date = 0;
        $quote->subscription_id = 8;

        $quote->method('recurringDates')->willReturn(['2025-12-06', '2025-12-13']);

        $transformer = new RecurringQuoteTransformer();
        $data = $transformer->transform($quote);

        $this->assertEquals($transformer->encodePrimaryKey(1), $data['id']);
        $this->assertEquals(['2025-12-06', '2025-12-13'], $data['recurring_dates']);
        $this->assertEquals('Public Note', $data['public_notes']);
        $this->assertEquals('Private Note', $data['private_notes']);
    }
}

