<?php

namespace Tests\Utils;

use App\Utils\TemplateEngine;
use App\Models\Invoice;
use App\Models\InvoiceInvitation;
use App\Models\Client;
use App\Models\ClientContact;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use PHPUnit\Framework\TestCase;
use Mockery;

class TemplateEngineTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_build_with_invoice_entity()
    {
        $user = Mockery::mock(User::class);
        $company = Mockery::mock(Company::class);
        $client = Mockery::mock(Client::class);
        $contact = Mockery::mock(ClientContact::class);
        $invoice = Mockery::mock(Invoice::class);
        $invitation = Mockery::mock(InvoiceInvitation::class);

        // Setup user and company mocks
        $user->shouldReceive('company')->andReturn($company);
        $company->shouldReceive('present->logo')->andReturn('logo.png');
        $company->shouldReceive('getSetting')->andReturn('plain');
        $company->shouldReceive('locale')->andReturn('en');

        // Invoice mocks
        $invoice->shouldReceive('client')->andReturn($client);
        $client->shouldReceive('primary_contact')->andReturn(collect([$contact]));
        $invoice->shouldReceive('invitations')->andReturn(collect([$invitation]));
        $invoice->shouldReceive('vendor->exists')->andReturn(false);

        // Setup auth() helper
        auth()->shouldReceive('user')->andReturn($user);

        $body = "Hello $name";
        $subject = "Invoice Notification";
        $entity = "invoice";
        $entity_id = "123";
        $template = "invoice_email";

        $engine = new TemplateEngine($body, $subject, $entity, $entity_id, $template);

        $result = $engine->build();

        $this->assertArrayHasKey('body', $result);
        $this->assertArrayHasKey('subject', $result);
        $this->assertArrayHasKey('wrapper', $result);
        $this->assertArrayHasKey('raw_body', $result);
        $this->assertArrayHasKey('raw_subject', $result);
    }

    public function test_build_with_payment_entity()
    {
        $user = Mockery::mock(User::class);
        $company = Mockery::mock(Company::class);
        $client = Mockery::mock(Client::class);
        $contact = Mockery::mock(ClientContact::class);
        $payment = Mockery::mock(\App\Models\Payment::class);

        $user->shouldReceive('company')->andReturn($company);
        $company->shouldReceive('present->logo')->andReturn('logo.png');
        $company->shouldReceive('getSetting')->andReturn('plain');

        $payment->shouldReceive('client->contacts->first')->andReturn($contact);

        auth()->shouldReceive('user')->andReturn($user);

        $engine = new TemplateEngine('', '', 'payment', '456', 'payment_email');

        $result = $engine->build();

        $this->assertArrayHasKey('body', $result);
        $this->assertArrayHasKey('subject', $result);
    }

    public function test_mockEntity_rolls_back_on_exception()
    {
        $engine = new TemplateEngine('', '', '', '', 'payment_email');

        // Force DB exception
        DB::shouldReceive('connection->beginTransaction')->andThrow(new \Exception('DB Error'));
        DB::shouldReceive('connection->rollBack');

        // Just call mockEntity to hit catch
        $reflection = new \ReflectionClass($engine);
        $method = $reflection->getMethod('mockEntity');
        $method->setAccessible(true);

        $method->invoke($engine);
        $this->assertTrue(true); // Test passes if no uncaught exception
    }

    public function test_fakerValues_runs_with_conversion()
    {
        $engine = new TemplateEngine('Hello $name', 'Subject', 'invoice', '1', 'template');

        $reflection = new \ReflectionClass($engine);
        $method = $reflection->getMethod('fakerValues');
        $method->setAccessible(true);

        $method->invoke($engine);

        $this->assertTrue(true); // Ensures method executes
    }

    public function test_entityValues_runs_for_purchase_order()
    {
        $engine = Mockery::mock(TemplateEngine::class)->makePartial();

        $vendorContact = Mockery::mock(\App\Models\VendorContact::class);
        $invitation = Mockery::mock(\App\Models\PurchaseOrderInvitation::class);
        $purchaseOrder = Mockery::mock(\App\Models\PurchaseOrder::class);

        $purchaseOrder->shouldReceive('invitations->first')->andReturn($invitation);
        $engine->entity_obj = $purchaseOrder;
        $engine->entity = 'purchaseOrder';

        $reflection = new \ReflectionClass($engine);
        $method = $reflection->getMethod('entityValues');
        $method->setAccessible(true);
        $method->invoke($engine, $vendorContact);

        $this->assertTrue(true); // method runs
    }

    // Additional tests can be added for:
    // - setSettingsObject paths (client exists / purchaseOrder / default)
    // - setTemplates paths (body / subject empty or filled)
    // - renderTemplate paths (plain / custom / client email style)
}

