<?php

namespace Tests\Unit\Transformers\Contact;

use App\Models\Invoice;
use App\Transformers\Contact\InvoiceTransformer;
use Tests\TestCase;

class InvoiceTransformerTest extends TestCase
{
    /** @test */
    public function test_invoice_transformer_returns_correct_array()
    {
        // Create invoice WITHOUT touching database
        $invoice = new Invoice();
        $invoice->id = 10;
        $invoice->amount = 200.50;
        $invoice->balance = 150.25;
        $invoice->status_id = 2;
        $invoice->updated_at = now();
        $invoice->deleted_at = null;
        $invoice->number = 'INV-001';
        $invoice->discount = 5.0;
        $invoice->po_number = 'PO-111';
        $invoice->date = '2025-01-01';
        $invoice->due_date = '2025-01-10';
        $invoice->terms = 'Payment due in 10 days';
        $invoice->public_notes = 'Thank you';
        $invoice->is_deleted = false;
        $invoice->tax_name1 = 'GST';
        $invoice->tax_rate1 = 5;
        $invoice->tax_name2 = 'VAT';
        $invoice->tax_rate2 = 10;
        $invoice->tax_name3 = 'Service';
        $invoice->tax_rate3 = 2;
        $invoice->is_amount_discount = true;
        $invoice->footer = 'Footer text';
        $invoice->partial = 50;
        $invoice->partial_due_date = '2025-01-05';
        $invoice->custom_value1 = 1.5;
        $invoice->custom_value2 = 2.5;
        $invoice->custom_value3 = true;
        $invoice->custom_value4 = false;
        $invoice->line_items = [];

        $transformer = new InvoiceTransformer();
        $result = $transformer->transform($invoice);

        // hashed id
        $this->assertIsString($result['id']);
        $this->assertNotEquals('10', $result['id']);

        // assert values
        $this->assertSame(200.50, $result['amount']);
        $this->assertSame(150.25, $result['balance']);
        $this->assertSame(2, $result['status_id']);
        $this->assertSame('INV-001', $result['number']);
        $this->assertSame('2025-01-01', $result['date']);
        $this->assertSame([], $result['line_items']);
    }
}

