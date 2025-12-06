<?php

namespace Tests\Events\Invoice;

use Tests\TestCase;
use App\Models\Invoice;
use App\Models\Company;
use App\Models\InvoiceInvitation;
use League\Fractal\Manager;

//InvoiceAutoBillFailed

class InvoicesTest extends TestCase
{
    public function test_invoice_auto_bill_failed_properties_are_set()
    {
        $invoice = new Invoice();
        $company = new Company();
        $event_vars = ['attempt' => 1];
        $notes = 'Card declined';

        $event = new \App\Events\Invoice\InvoiceAutoBillFailed(
            $invoice,
            $company,
            $event_vars,
            $notes
        );

        $this->assertSame($invoice, $event->invoice);
        $this->assertSame($company, $event->company);
        $this->assertSame($event_vars, $event->event_vars);
        $this->assertSame($notes, $event->notes);
    }

	// InvoiceAutoBillSuccess

    public function test_invoice_auto_bill_success_properties_are_set()
    {
        $invoice = new Invoice();
        $company = new Company();
        $event_vars = ['status' => 'paid'];

        $event = new \App\Events\Invoice\InvoiceAutoBillSuccess(
            $invoice,
            $company,
            $event_vars
        );

        $this->assertSame($invoice, $event->invoice);
        $this->assertSame($company, $event->company);
        $this->assertSame($event_vars, $event->event_vars);
    }

    //InvoiceWasEmailedAndFailed
    
    public function test_invoice_was_emailed_and_failed_properties_are_set()
    {
        $invitation = (object)['id' => 9];
        $company = new Company();
        $message = 'SMTP error';
        $template = 'email_template';
        $event_vars = ['attempt' => 3];

        $event = new \App\Events\Invoice\InvoiceWasEmailedAndFailed(
            $invitation,
            $company,
            $message,
            $template,
            $event_vars
        );

        $this->assertSame($invitation, $event->invitation);
        $this->assertSame($company, $event->company);
        $this->assertSame($message, $event->message);
        $this->assertSame($template, $event->template);
        $this->assertSame($event_vars, $event->event_vars);
    }

    //InvoiceWasMarkedSent

    public function test_invoice_was_marked_sent_properties_are_set()
    {
        $invoice = new Invoice();
        $company = new Company();
        $event_vars = ['user' => 'admin'];

        $event = new \App\Events\Invoice\InvoiceWasMarkedSent(
            $invoice,
            $company,
            $event_vars
        );

        $this->assertSame($invoice, $event->invoice);
        $this->assertSame($company, $event->company);
        $this->assertSame($event_vars, $event->event_vars);
    }

    //InvoiceWasViewed

    public function test_invoice_was_viewed_properties_are_set()
    {
        $invoice = new Invoice();
        $invitation = new InvoiceInvitation();
        $invitation->invoice = $invoice;

        $company = new Company();
        $event_vars = ['ip' => '1.1.1.1'];

        $event = new \App\Events\Invoice\InvoiceWasViewed(
            $invitation,
            $company,
            $event_vars
        );

        $this->assertSame($invitation, $event->invitation);
        $this->assertSame($company, $event->company);
        $this->assertSame($event_vars, $event->event_vars);
    }

    public function test_invoice_was_viewed_broadcast_model()
    {
        $invoice = new Invoice();
        $invitation = new InvoiceInvitation();
        $invitation->invoice = $invoice;

        $event = new \App\Events\Invoice\InvoiceWasViewed(
            $invitation,
            new Company(),
            []
        );

        $this->assertSame($invoice, $event->broadcastModel());
    }

    public function test_invoice_was_viewed_broadcast_manager_and_includes()
    {
        $manager = new Manager();
        $invoice = new Invoice();
        $invitation = new InvoiceInvitation();
        $invitation->invoice = $invoice;

        $event = new \App\Events\Invoice\InvoiceWasViewed(
            $invitation,
            new Company(),
            []
        );

        $returnedManager = $event->broadcastManager($manager);

        $this->assertSame($manager, $returnedManager);
        $this->assertEquals(['client'], $event->broadcastIncludes());
    }
}

