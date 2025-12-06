<?php

namespace Tests\Unit\Transformers;

use App\Models\Activity;
use App\Models\Backup;
use App\Models\Client;
use App\Models\Document;
use App\Models\Location;
use App\Models\Quote;
use App\Models\QuoteInvitation;
use App\Transformers\QuoteTransformer;
use App\Transformers\ActivityTransformer;
use App\Transformers\ClientTransformer;
use App\Transformers\DocumentTransformer;
use App\Transformers\QuoteInvitationTransformer;
use App\Transformers\LocationTransformer;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Mockery;
use PHPUnit\Framework\TestCase;

class QuoteTransformerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function makeQuoteMock(): Quote
    {
        $quote = Mockery::mock(Quote::class)->makePartial();

        $quote->id = 1;
        $quote->user_id = 2;
        $quote->assigned_user_id = 3;
        $quote->amount = 1000.0;
        $quote->balance = 500.0;
        $quote->client_id = 4;
        $quote->status_id = 1;
        $quote->design_id = 5;
        $quote->invoice_id = 6;
        $quote->vendor_id = 7;
        $quote->updated_at = time();
        $quote->deleted_at = null;
        $quote->created_at = time();
        $quote->number = 'Q-001';
        $quote->discount = 50.0;
        $quote->po_number = 'PO-123';
        $quote->date = '2025-12-06';
        $quote->last_sent_date = null;
        $quote->next_send_date = null;
        $quote->reminder1_sent = null;
        $quote->reminder2_sent = null;
        $quote->reminder3_sent = null;
        $quote->reminder_last_sent = null;
        $quote->due_date = null;
        $quote->terms = 'Terms';
        $quote->public_notes = 'Public Notes';
        $quote->private_notes = 'Private Notes';
        $quote->is_deleted = false;
        $quote->uses_inclusive_taxes = false;
        $quote->tax_name1 = 'Tax1';
        $quote->tax_rate1 = 5.0;
        $quote->tax_name2 = '';
        $quote->tax_rate2 = 0.0;
        $quote->tax_name3 = '';
        $quote->tax_rate3 = 0.0;
        $quote->total_taxes = 50.0;
        $quote->is_amount_discount = true;
        $quote->footer = 'Footer';
        $quote->partial = 100.0;
        $quote->partial_due_date = null;
        $quote->custom_value1 = 'CV1';
        $quote->custom_value2 = 'CV2';
        $quote->custom_value3 = 'CV3';
        $quote->custom_value4 = 'CV4';
        $quote->has_tasks = true;
        $quote->has_expenses = true;
        $quote->custom_surcharge1 = 10.0;
        $quote->custom_surcharge2 = 0.0;
        $quote->custom_surcharge3 = 0.0;
        $quote->custom_surcharge4 = 0.0;
        $quote->custom_surcharge_tax1 = true;
        $quote->custom_surcharge_tax2 = false;
        $quote->custom_surcharge_tax3 = false;
        $quote->custom_surcharge_tax4 = false;
        $quote->line_items = ['item1', 'item2'];
        $quote->exchange_rate = 1.0;
        $quote->paid_to_date = 500.0;
        $quote->project_id = 8;
        $quote->subscription_id = 9;
        $quote->tax_data = null;
        $quote->e_invoice = null;
        $quote->location_id = 10;

        // Relations
        $quote->activities = [Mockery::mock(Activity::class)];
        $quote->invitations = [Mockery::mock(QuoteInvitation::class)];
        $quote->history = [Mockery::mock(Backup::class)];
        $quote->documents = [Mockery::mock(Document::class)];
        $quote->client = Mockery::mock(Client::class);
        $quote->location = Mockery::mock(Location::class);

        return $quote;
    }

    /** @test */
    public function transform_returns_correct_array()
    {
        $quote = $this->makeQuoteMock();
        $transformer = Mockery::mock(QuoteTransformer::class)->makePartial();
        $transformer->shouldAllowMockingProtectedMethods();
        $transformer->shouldReceive('encodePrimaryKey')->andReturnUsing(fn($id) => (string)$id);

        $data = $transformer->transform($quote);

        $this->assertIsArray($data);
        $this->assertEquals('1', $data['id']);
        $this->assertEquals('2', $data['user_id']);
        $this->assertEquals('3', $data['assigned_user_id']);
        $this->assertEquals(1000.0, $data['amount']);
        $this->assertEquals(500.0, $data['balance']);
        $this->assertEquals('4', $data['client_id']);
        $this->assertEquals('Q-001', $data['number']);
        $this->assertEquals('Public Notes', $data['public_notes']);
        $this->assertEquals('Private Notes', $data['private_notes']);
    }

    /** @test */
    public function include_collections_return_collection()
    {
        $quote = $this->makeQuoteMock();
        $transformer = new QuoteTransformer();

        $methods = ['includeActivities', 'includeInvitations', 'includeHistory', 'includeDocuments'];

        foreach ($methods as $method) {
            $resource = $transformer->$method($quote);
            $this->assertInstanceOf(Collection::class, $resource);
            $this->assertNotEmpty($resource->getData());
        }
    }

    /** @test */
    public function include_item_returns_item_or_null()
    {
        $quote = $this->makeQuoteMock();
        $transformer = new QuoteTransformer();

        // client present
        $resource = $transformer->includeClient($quote);
        $this->assertInstanceOf(Item::class, $resource);
        $this->assertNotNull($resource->getData());

        // location present
        $resource = $transformer->includeLocation($quote);
        $this->assertInstanceOf(Item::class, $resource);

        // location null
        $quote->location = null;
        $resource = $transformer->includeLocation($quote);
        $this->assertNull($resource);
    }
}

