<?php

namespace Tests\Unit\Transformers;

use App\Models\Activity;
use App\Models\Backup;
use App\Models\Client;
use App\Models\Credit;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\InvoiceInvitation;
use App\Models\Payment;
use App\Models\Location;
use App\Transformers\InvoiceTransformer;
use App\Transformers\LocationTransformer;
use App\Transformers\InvoiceInvitationTransformer;
use App\Transformers\InvoiceHistoryTransformer;
use App\Transformers\ClientTransformer;
use App\Transformers\PaymentTransformer;
use App\Transformers\CreditTransformer;
use App\Transformers\DocumentTransformer;
use App\Transformers\ActivityTransformer;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use Mockery;
use PHPUnit\Framework\TestCase;

class InvoiceTransformerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Create a mock Invoice with dummy data and relationships
     */
    protected function makeInvoiceMock(): Invoice
    {
        $invoice = Mockery::mock(Invoice::class)->makePartial();

        $invoice->id = 1;
        $invoice->user_id = 2;
        $invoice->project_id = 3;
        $invoice->assigned_user_id = 4;
        $invoice->amount = 100;
        $invoice->balance = 50;
        $invoice->client_id = 5;
        $invoice->vendor_id = 6;
        $invoice->status_id = 1;
        $invoice->design_id = 7;
        $invoice->recurring_id = 8;
        $invoice->created_at = time();
        $invoice->updated_at = time();
        $invoice->deleted_at = null;
        $invoice->is_deleted = false;
        $invoice->number = 'INV-001';
        $invoice->location_id = 9;
        $invoice->line_items = [];

        // Relations
        $invoice->location = Mockery::mock(Location::class);
        $invoice->invitations = [Mockery::mock(InvoiceInvitation::class)];
        $invoice->history = [Mockery::mock(Backup::class)];
        $invoice->client = Mockery::mock(Client::class);
        $invoice->payments = [Mockery::mock(Payment::class)];
        $invoice->credits = [Mockery::mock(Credit::class)];
        $invoice->documents = [Mockery::mock(Document::class)];
        $invoice->activities = [Mockery::mock(Activity::class)];

        // Methods used in transform
        $invoice->reminderSchedule = fn() => '1';
        $invoice->isLocked = fn() => true;
        $invoice->paymentSchedule = fn() => [];

        return $invoice;
    }

    /** @test */
    public function transform_returns_all_fields()
    {
        $invoice = $this->makeInvoiceMock();

        $transformer = Mockery::mock(InvoiceTransformer::class)->makePartial();
        $transformer->shouldAllowMockingProtectedMethods();
        $transformer->shouldReceive('encodePrimaryKey')->andReturnUsing(fn($id) => (string) $id);

        // Inject mock request into container to avoid request() errors
        $mockRequest = Mockery::mock(\Illuminate\Http\Request::class);
        $mockRequest->shouldReceive('has')->andReturnFalse();
        $mockRequest->shouldReceive('query')->andReturnNull();
        app()->instance('request', $mockRequest);

        $data = $transformer->transform($invoice);

        $this->assertIsArray($data);
        $this->assertEquals('invoice', $data['entity_type']);
        $this->assertEquals('INV-001', $data['number']);
        $this->assertEquals('1', $data['id']);
        $this->assertEquals('2', $data['user_id']);
        $this->assertEquals('5', $data['client_id']);
        $this->assertEquals('9', $data['location_id']);
    }

    /** @test */
    public function transform_respects_request_flags()
    {
        $invoice = $this->makeInvoiceMock();

        $transformer = Mockery::mock(InvoiceTransformer::class)->makePartial();
        $transformer->shouldAllowMockingProtectedMethods();
        $transformer->shouldReceive('encodePrimaryKey')->andReturnUsing(fn($id) => (string) $id);

        $mockRequest = Mockery::mock(\Illuminate\Http\Request::class);
        $mockRequest->shouldReceive('has')->with('reminder_schedule')->andReturnTrue();
        $mockRequest->shouldReceive('query')->with('reminder_schedule')->andReturn('true');
        $mockRequest->shouldReceive('has')->with('is_locked')->andReturnTrue();
        $mockRequest->shouldReceive('query')->with('is_locked')->andReturn('true');
        $mockRequest->shouldReceive('has')->with('show_schedule')->andReturnTrue();
        $mockRequest->shouldReceive('query')->with('show_schedule')->andReturn('true');
        app()->instance('request', $mockRequest);

        $data = $transformer->transform($invoice);

        $this->assertArrayHasKey('reminder_schedule', $data);
        $this->assertArrayHasKey('is_locked', $data);
        $this->assertArrayHasKey('schedule', $data);
    }

    /** @test */
    public function include_methods_return_resources()
    {
        $invoice = $this->makeInvoiceMock();
        $transformer = new InvoiceTransformer();

        $includes = [
            'includeLocation' => Item::class,
            'includeClient' => Item::class,
            'includeInvitations' => Collection::class,
            'includeHistory' => Collection::class,
            'includePayments' => Collection::class,
            'includeCredits' => Collection::class,
            'includeDocuments' => Collection::class,
            'includeActivities' => Collection::class,
        ];

        foreach ($includes as $method => $expected) {
            $resource = $transformer->$method($invoice);
            $this->assertInstanceOf($expected, $resource);
        }
    }

    /** @test */
    public function include_methods_empty_relations()
    {
        $invoice = Mockery::mock(Invoice::class)->makePartial();
        $invoice->location = null;
        $invoice->client = null;
        $invoice->invitations = [];
        $invoice->history = [];
        $invoice->payments = [];
        $invoice->credits = [];
        $invoice->documents = [];
        $invoice->activities = [];

        $transformer = new InvoiceTransformer();

        $this->assertNull(Item::class, $transformer->includeLocation($invoice));
        $this->assertNull($transformer->includeLocation($invoice)->getData());

        $this->assertInstanceOf(Item::class, $transformer->includeClient($invoice));
        $this->assertNull($transformer->includeClient($invoice)->getData());

        $collectionMethods = [
            'includeInvitations',
            'includeHistory',
            'includePayments',
            'includeCredits',
            'includeDocuments',
            'includeActivities',
        ];

        foreach ($collectionMethods as $method) {
            $res = $transformer->$method($invoice);
            $this->assertInstanceOf(Collection::class, $res);
            $this->assertEmpty($res->getData());
        }
    }
}

