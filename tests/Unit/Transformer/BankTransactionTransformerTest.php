<?php

namespace Tests\Unit\Transformers;

use App\Models\BankTransaction;
use App\Models\Company;
use App\Models\Vendor;
use App\Models\Payment;
use App\Transformers\BankTransactionTransformer;
use PHPUnit\Framework\TestCase;
use Mockery;
use League\Fractal\Resource\Item;

class BankTransactionTransformerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_transform_returns_correct_array()
    {
        $transaction = Mockery::mock(BankTransaction::class)->makePartial();

        $transaction->id = 1;
        $transaction->user_id = 2;
        $transaction->bank_integration_id = 3;
        $transaction->transaction_id = 123;
        $transaction->amount = 250.50;
        $transaction->currency_id = 'USD';
        $transaction->account_type = 'checking';
        $transaction->category_id = 10;
        $transaction->ninja_category_id = 20;
        $transaction->category_type = 'income';
        $transaction->date = '2025-12-01';
        $transaction->bank_account_id = 5;
        $transaction->status_id = '1';
        $transaction->description = 'Test transaction';
        $transaction->participant = 'John Doe';
        $transaction->participant_name = 'John Doe';
        $transaction->base_type = 'bank';
        $transaction->invoice_ids = 'INV-123';
        $transaction->expense_id = 7;
        $transaction->payment_id = 8;
        $transaction->vendor_id = 9;
        $transaction->bank_transaction_rule_id = 11;
        $transaction->is_deleted = false;
        $transaction->nordigen_transaction_id = 'nord123';
        $transaction->created_at = 1700000000;
        $transaction->updated_at = 1700000100;
        $transaction->deleted_at = null;

        $transformer = Mockery::mock(BankTransactionTransformer::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $transformer->shouldReceive('encodePrimaryKey')
            ->andReturnUsing(fn($id) => (string)$id);

        $data = $transformer->transform($transaction);

        $this->assertEquals('1', $data['id']);
        $this->assertEquals(250.50, $data['amount']);
        $this->assertEquals('USD', $data['currency_id']);
        $this->assertEquals('Test transaction', $data['description']);
        $this->assertFalse($data['is_deleted']);
        $this->assertEquals('0', $data['archived_at']);
    }

    public function test_include_company_returns_item_or_null()
    {
        $transaction = Mockery::mock(BankTransaction::class)->makePartial();
        $transformer = new BankTransactionTransformer();

        $transaction->company = null;
        $result = $transformer->includeCompany($transaction);
        $this->assertInstanceOf(Item::class, $result);
        $this->assertNull($result->getData());

        $transaction->company = Mockery::mock(Company::class);
        $result = $transformer->includeCompany($transaction);
        $this->assertInstanceOf(Item::class, $result);
        $this->assertNotNull($result->getData());
    }

    public function test_include_vendor_returns_item_or_null()
    {
        $transaction = Mockery::mock(BankTransaction::class)->makePartial();
        $transformer = new BankTransactionTransformer();

        $transaction->vendor = null;
        $result = $transformer->includeVendor($transaction);
        $this->assertInstanceOf(Item::class, $result);
        $this->assertNull($result->getData());

        $transaction->vendor = Mockery::mock(Vendor::class);
        $result = $transformer->includeVendor($transaction);
        $this->assertInstanceOf(Item::class, $result);
        $this->assertNotNull($result->getData());
    }

    public function test_include_payment_returns_item_or_null()
    {
        $transaction = Mockery::mock(BankTransaction::class)->makePartial();
        $transformer = new BankTransactionTransformer();

        $transaction->payment = null;
        $result = $transformer->includePayment($transaction);
        $this->assertInstanceOf(Item::class, $result);
        $this->assertNull($result->getData());

        $transaction->payment = Mockery::mock(Payment::class);
        $result = $transformer->includePayment($transaction);
        $this->assertInstanceOf(Item::class, $result);
        $this->assertNotNull($result->getData());
    }
}

