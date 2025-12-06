<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Credit;
use App\Models\Client;
use App\Models\Location;
use App\Models\CreditInvitation;
use App\Models\Document;
use App\Models\Activity;
use App\Models\Backup;
use App\Transformers\CreditTransformer;
use League\Fractal\Manager;

class CreditTransformerTest extends TestCase
{
    public function testTransformAndIncludes()
    {
        // Create a fake Credit model
        $credit = new Credit();
        $credit->id = 1;
        $credit->user_id = 2;
        $credit->project_id = 3;
        $credit->assigned_user_id = 4;
        $credit->vendor_id = 5;
        $credit->amount = 100.50;
        $credit->balance = 50.25;
        $credit->client_id = 6;
        $credit->status_id = null; // Should default to 1
        $credit->design_id = 7;
        $credit->created_at = time();
        $credit->updated_at = time();
        $credit->deleted_at = null;
        $credit->is_deleted = false;
        $credit->number = 'CR-001';
        $credit->discount = 5.0;
        $credit->po_number = 'PO-123';
        $credit->date = '2025-12-05';
        $credit->last_sent_date = null;
        $credit->next_send_date = null;
        $credit->reminder1_sent = null;
        $credit->reminder2_sent = null;
        $credit->reminder3_sent = null;
        $credit->reminder_last_sent = null;
        $credit->due_date = '2025-12-31';
        $credit->terms = 'Net 30';
        $credit->public_notes = 'Note';
        $credit->private_notes = 'Private Note';
        $credit->uses_inclusive_taxes = true;
        $credit->tax_name1 = 'VAT';
        $credit->tax_rate1 = 10;
        $credit->tax_name2 = null;
        $credit->tax_rate2 = 0;
        $credit->tax_name3 = null;
        $credit->tax_rate3 = 0;
        $credit->total_taxes = 10;
        $credit->is_amount_discount = true;
        $credit->footer = 'Footer Text';
        $credit->partial = 20.0;
        $credit->partial_due_date = '2025-12-20';
        $credit->custom_value1 = 'Custom1';
        $credit->custom_value2 = 'Custom2';
        $credit->custom_value3 = null;
        $credit->custom_value4 = null;
        $credit->has_tasks = true;
        $credit->has_expenses = false;
        $credit->custom_surcharge1 = 1.0;
        $credit->custom_surcharge2 = 2.0;
        $credit->custom_surcharge3 = 0;
        $credit->custom_surcharge4 = 0;
        $credit->custom_surcharge_tax1 = true;
        $credit->custom_surcharge_tax2 = false;
        $credit->custom_surcharge_tax3 = false;
        $credit->custom_surcharge_tax4 = false;
        $credit->line_items = [];
        $credit->exchange_rate = 1.2;
        $credit->paid_to_date = 50.25;
        $credit->subscription_id = 8;
        $credit->invoice_id = 9;
        $credit->tax_data = ['some' => 'tax'];
        $credit->e_invoice = ['some' => 'invoice'];
        $credit->location_id = 10;

        // Relationships
        $credit->client = new Client();
        $credit->documents = collect([new Document()]);
        $credit->invitations = collect([new CreditInvitation()]);
        $credit->activities = collect([new Activity()]);
        $credit->history = collect([new Backup()]);
        $credit->location = new Location();

        $fractal = new Manager();
        $transformer = new CreditTransformer();

        // Transform method
        $result = $transformer->transform($credit);
        $this->assertEquals('CR-001', $result['number']);
        $this->assertEquals(1, $result['status_id']); // default value works
        $this->assertEquals(100.50, $result['amount']);

        // Include methods
        $locationInclude = $transformer->includeLocation($credit);
        $this->assertNotNull($locationInclude);

        $activitiesInclude = $transformer->includeActivities($credit);
        $this->assertNotNull($activitiesInclude);

        $historyInclude = $transformer->includeHistory($credit);
        $this->assertNotNull($historyInclude);

        $invitationsInclude = $transformer->includeInvitations($credit);
        $this->assertNotNull($invitationsInclude);

        $clientInclude = $transformer->includeClient($credit);
        $this->assertNotNull($clientInclude);

        $documentsInclude = $transformer->includeDocuments($credit);
        $this->assertNotNull($documentsInclude);
    }
}

