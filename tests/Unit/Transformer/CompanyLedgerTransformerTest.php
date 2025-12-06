<?php

namespace Tests\Unit\Transformers;

use App\Models\CompanyLedger;
use App\Transformers\CompanyLedgerTransformer;
use Tests\TestCase;

class CompanyLedgerTransformerTest extends TestCase
{
    private CompanyLedgerTransformer $transformer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transformer = new CompanyLedgerTransformer();
    }

    /** @test */
    public function test_transform_returns_correct_array()
    {
        $ledger = new CompanyLedger();
        $ledger->company_ledgerable_type = 'App\Models\Client';
        $ledger->company_ledgerable_id = 10;
        $ledger->notes = 'Ledger notes';
        $ledger->balance = 100.50;
        $ledger->adjustment = 10.25;
        $ledger->activity_id = 5;
        $ledger->created_at = 1699999999;
        $ledger->updated_at = 1700000000;
        $ledger->deleted_at = null;

        $result = $this->transformer->transform($ledger);

        $entityKey = 'client_id'; // lcfirst(rtrim('Client', 's')) . '_id'

        $this->assertArrayHasKey($entityKey, $result);
        $this->assertEquals($this->transformer->encodePrimaryKey(10), $result[$entityKey]);
        $this->assertEquals('Ledger notes', $result['notes']);
        $this->assertEquals(100.50, $result['balance']);
        $this->assertEquals(10.25, $result['adjustment']);
        $this->assertEquals(5, $result['activity_id']);
        $this->assertEquals(1699999999, $result['created_at']);
        $this->assertEquals(1700000000, $result['updated_at']);
        $this->assertEquals(0, $result['archived_at']); // deleted_at is null
    }
}

