<?php

namespace Tests\Unit\Transformers;

use App\Models\BankTransactionRule;
use App\Models\Client;
use App\Models\Company;
use App\Models\Vendor;
use App\Models\ExpenseCategory;
use App\Transformers\BankTransactionRuleTransformer;
use League\Fractal\Resource\Item;
use Tests\TestCase;

class BankTransactionRuleTransformerTest extends TestCase
{
    private BankTransactionRuleTransformer $transformer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transformer = new BankTransactionRuleTransformer();
    }
/** @test */
public function test_transform_returns_correct_array()
{
    $rule = new BankTransactionRule();
    $rule->id = 1;
    $rule->name = 'Test Rule';
    $rule->rules = ['condition' => 'amount > 100'];
    $rule->auto_convert = true;
    $rule->matches_on_all = false;
    $rule->applies_to = 'invoice';
    $rule->client_id = 10;
    $rule->vendor_id = 20;
    $rule->category_id = 30;
    $rule->is_deleted = false;
    $rule->created_at = 1699999999;
    $rule->updated_at = 1700000000;
    $rule->deleted_at = null;

    $transformer = new BankTransactionRuleTransformer();

    $result = $transformer->transform($rule);

    $this->assertIsArray($result);
    $this->assertEquals('VolejRejNm', $result['id']);
    $this->assertEquals('Test Rule', $result['name']);
    $this->assertEquals(['condition' => 'amount > 100'], $result['rules']);
    $this->assertTrue($result['auto_convert']);
    $this->assertFalse($result['matches_on_all']);
    $this->assertEquals('invoice', $result['applies_to']);
    $this->assertEquals('10', $result['client_id']);
    $this->assertEquals('20', $result['vendor_id']);
    $this->assertEquals('30', $result['category_id']);
    $this->assertFalse($result['is_deleted']);
    $this->assertEquals(1699999999, $result['created_at']);
    $this->assertEquals(1700000000, $result['updated_at']);
    $this->assertEquals(0, $result['archived_at']); // deleted_at is null
}



    /** @test */
    public function test_include_company_returns_item()
    {
        $company = new Company();
        $bankTransactionRule = new BankTransactionRule();
        $bankTransactionRule->company = $company;

        $resource = $this->transformer->includeCompany($bankTransactionRule);

        $this->assertInstanceOf(Item::class, $resource);
        $this->assertEquals(Company::class, $resource->getResourceKey());
        $this->assertNotNull($resource->getData());
    }

    /** @test */
    public function test_include_client_returns_item_or_null()
    {
        $client = new Client();
        $bankTransactionRule = new BankTransactionRule();
        $bankTransactionRule->client = $client;

        $resource = $this->transformer->includeClient($bankTransactionRule);

        $this->assertInstanceOf(Item::class, $resource);
        $this->assertEquals(Client::class, $resource->getResourceKey());
        $this->assertNotNull($resource->getData());
    }

    /** @test */
    public function test_include_vendor_returns_item_or_null()
    {
        $vendor = new Vendor();
        $bankTransactionRule = new BankTransactionRule();
        $bankTransactionRule->vendor = $vendor;

        $resource = $this->transformer->includeVendor($bankTransactionRule);

        $this->assertInstanceOf(Item::class, $resource);
        $this->assertEquals(Vendor::class, $resource->getResourceKey());
        $this->assertNotNull($resource->getData());
    }

    /** @test */
    public function test_include_expense_category_returns_item_or_null()
    {
        $expenseCategory = new ExpenseCategory();
        $bankTransactionRule = new BankTransactionRule();
        $bankTransactionRule->expense_category = $expenseCategory;

        $resource = $this->transformer->includeExpenseCategory($bankTransactionRule);

        $this->assertInstanceOf(Item::class, $resource);
        $this->assertEquals(ExpenseCategory::class, $resource->getResourceKey());
        $this->assertNotNull($resource->getData());
    }

    /** @test */
    public function test_include_methods_return_null_when_relation_missing()
    {
        $rule = new BankTransactionRule();
        $rule->client = null;
        $rule->vendor = null;
        $rule->expense_category = null;

        // includeClient, includeVendor, includeExpenseCategory return literal null when missing
        $this->assertNull($this->transformer->includeClient($rule));
        $this->assertNull($this->transformer->includeVendor($rule));
        $this->assertNull($this->transformer->includeExpenseCategory($rule));

        // includeCompany never returns literal null, instead returns Item with null data
        $companyResource = $this->transformer->includeCompany($rule);
        $this->assertInstanceOf(Item::class, $companyResource);
        $this->assertNull($companyResource->getData());
    }
}

