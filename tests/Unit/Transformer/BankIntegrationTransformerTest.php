<?php

namespace Tests\Unit\Transformers;

use App\Models\BankIntegration;
use App\Models\Account;
use App\Models\Company;
use App\Models\BankTransaction;
use App\Transformers\BankIntegrationTransformer;
use App\Transformers\AccountTransformer;
use App\Transformers\CompanyTransformer;
use App\Transformers\BankTransactionTransformer;
use PHPUnit\Framework\TestCase;
use Mockery;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;

class BankIntegrationTransformerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_transform_returns_correct_array()
    {
        $bankIntegration = Mockery::mock(BankIntegration::class)->makePartial();

        $bankIntegration->id = 1;
        $bankIntegration->provider_name = 'Test Provider';
        $bankIntegration->provider_id = 101;
        $bankIntegration->bank_account_id = 202;
        $bankIntegration->bank_account_name = 'Test Account';
        $bankIntegration->bank_account_number = '12345678';
        $bankIntegration->bank_account_status = 'active';
        $bankIntegration->bank_account_type = 'checking';
        $bankIntegration->nordigen_institution_id = 'nord123';
        $bankIntegration->balance = 500.25;
        $bankIntegration->currency = 'USD';
        $bankIntegration->nickname = 'My Bank';
        $bankIntegration->from_date = '2025-12-01';
        $bankIntegration->is_deleted = false;
        $bankIntegration->disabled_upstream = false;
        $bankIntegration->auto_sync = true;
        $bankIntegration->created_at = 1700000000;
        $bankIntegration->updated_at = 1700000100;
        $bankIntegration->deleted_at = null;
        $bankIntegration->integration_type = 'manual';

        $transformer = Mockery::mock(BankIntegrationTransformer::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $transformer->shouldReceive('encodePrimaryKey')
            ->andReturnUsing(fn($id) => (string)$id);

        $data = $transformer->transform($bankIntegration);

        $this->assertEquals('1', $data['id']);
        $this->assertEquals('Test Provider', $data['provider_name']);
        $this->assertEquals(101, $data['provider_id']);
        $this->assertEquals(202, $data['bank_account_id']);
        $this->assertEquals('Test Account', $data['bank_account_name']);
        $this->assertEquals('12345678', $data['bank_account_number']);
        $this->assertEquals('active', $data['bank_account_status']);
        $this->assertEquals('checking', $data['bank_account_type']);
        $this->assertEquals('nord123', $data['nordigen_institution_id']);
        $this->assertEquals(500.25, $data['balance']);
        $this->assertEquals('USD', $data['currency']);
        $this->assertEquals('My Bank', $data['nickname']);
        $this->assertEquals('2025-12-01', $data['from_date']);
        $this->assertFalse($data['is_deleted']);
        $this->assertFalse($data['disabled_upstream']);
        $this->assertTrue($data['auto_sync']);
        $this->assertEquals(1700000000, $data['created_at']);
        $this->assertEquals(1700000100, $data['updated_at']);
        $this->assertEquals('0', $data['archived_at']); // properly handle null
        $this->assertEquals('manual', $data['integration_type']);
    }

    public function test_include_account_returns_item_or_null()
    {
        $bankIntegration = Mockery::mock(BankIntegration::class)->makePartial();
        $transformer = new BankIntegrationTransformer();

        // No account
        $bankIntegration->account = null;
        $result = $transformer->includeAccount($bankIntegration);
        $this->assertInstanceOf(Item::class, $result);
        $this->assertNull($result->getData());

        // With account
        $bankIntegration->account = Mockery::mock(Account::class);
        $result = $transformer->includeAccount($bankIntegration);
        $this->assertInstanceOf(Item::class, $result);
        $this->assertNotNull($result->getData());
    }

    public function test_include_company_returns_item_or_null()
    {
        $bankIntegration = Mockery::mock(BankIntegration::class)->makePartial();
        $transformer = new BankIntegrationTransformer();

        // No company
        $bankIntegration->company = null;
        $result = $transformer->includeCompany($bankIntegration);
        $this->assertInstanceOf(Item::class, $result);
        $this->assertNull($result->getData());

        // With company
        $bankIntegration->company = Mockery::mock(Company::class);
        $result = $transformer->includeCompany($bankIntegration);
        $this->assertInstanceOf(Item::class, $result);
        $this->assertNotNull($result->getData());
    }

    public function test_include_bank_transactions_returns_collection_or_null()
    {
        $bankIntegration = Mockery::mock(BankIntegration::class)->makePartial();
        $transformer = new BankIntegrationTransformer();

        // No transactions
        $bankIntegration->transactions = collect();
        $result = $transformer->includeBankTransactions($bankIntegration);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEmpty($result->getData());

        // With transactions
        $transaction = Mockery::mock(BankTransaction::class);
        $bankIntegration->transactions = collect([$transaction]);
        $result = $transformer->includeBankTransactions($bankIntegration);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(1, $result->getData());
    }
}

