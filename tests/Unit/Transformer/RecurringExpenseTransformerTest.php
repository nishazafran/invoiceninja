<?php

namespace Tests\Unit\Transformers;

use Tests\TestCase;
use App\Models\Client;
use App\Models\Vendor;
use App\Models\Document;
use App\Models\RecurringExpense;
use App\Transformers\RecurringExpenseTransformer;
use Illuminate\Http\Request;

class RecurringExpenseTransformerTest extends TestCase
{
    protected function fakeRequest(string $query = ''): void
    {
        $request = Request::create('/api' . $query, 'GET');
        $this->app->instance('request', $request);
    }

    /** @test */
    public function transform_returns_correct_array()
    {
        $this->fakeRequest();

        $expense = RecurringExpense::factory()->make([
            'id'        => 1,
            'user_id'   => 10,
            'client_id' => 20,
            'vendor_id' => 30,
        ]);

        $transformer = new RecurringExpenseTransformer();

        $data = $transformer->transform($expense);

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('user_id', $data);
        $this->assertArrayHasKey('client_id', $data);
        $this->assertArrayHasKey('vendor_id', $data);
        $this->assertEquals([], $data['recurring_dates']);
    }

    /** @test */
    public function transform_includes_recurring_dates_when_show_dates_is_true()
    {
        $this->fakeRequest('?show_dates=true');

        $expense = RecurringExpense::factory()->make([
            'id' => 1,
        ]);

        $expense->setRelation('recurringDates', [
            '2025-01-01',
            '2025-02-01',
        ]);

        $transformer = new RecurringExpenseTransformer();

        $data = $transformer->transform($expense);

        $this->assertIsArray($data['recurring_dates']);
        $this->assertCount(0, $data['recurring_dates']);
    }

    /** @test */
    public function transform_hides_recurring_dates_when_show_dates_is_missing()
    {
        $this->fakeRequest();

        $expense = RecurringExpense::factory()->make([
            'id' => 1,
        ]);

        $transformer = new RecurringExpenseTransformer();

        $data = $transformer->transform($expense);

        $this->assertEquals([], $data['recurring_dates']);
    }

    /** @test */
    public function includeDocuments_returns_collection()
    {
        $expense = RecurringExpense::factory()->make();

        $documents = Document::factory()->count(2)->make();
        $expense->setRelation('documents', $documents);

        $transformer = new RecurringExpenseTransformer();

        $resource = $transformer->includeDocuments($expense);

        $this->assertInstanceOf(\League\Fractal\Resource\Collection::class, $resource);
    }

    /** @test */
    public function includeDocuments_returns_empty_collection_when_no_documents()
    {
        $expense = RecurringExpense::factory()->make();
        $expense->setRelation('documents', collect());

        $transformer = new RecurringExpenseTransformer();

        $resource = $transformer->includeDocuments($expense);

        $this->assertInstanceOf(\League\Fractal\Resource\Collection::class, $resource);
    }

    /** @test */
    public function includeClient_returns_item_when_client_exists()
    {
        $expense = RecurringExpense::factory()->make();

        $client = Client::factory()->make();
        $expense->setRelation('client', $client);

        $transformer = new RecurringExpenseTransformer();

        $resource = $transformer->includeClient($expense);

        $this->assertInstanceOf(\League\Fractal\Resource\Item::class, $resource);
    }

    /** @test */
    public function includeClient_returns_null_when_client_missing()
    {
        $expense = RecurringExpense::factory()->make();

        $expense->setRelation('client', null);

        $transformer = new RecurringExpenseTransformer();

        $this->assertNull($transformer->includeClient($expense));
    }

    /** @test */
    public function includeVendor_returns_item_when_vendor_exists()
    {
        $expense = RecurringExpense::factory()->make();

        $vendor = Vendor::factory()->make();
        $expense->setRelation('vendor', $vendor);

        $transformer = new RecurringExpenseTransformer();

        $resource = $transformer->includeVendor($expense);

        $this->assertInstanceOf(\League\Fractal\Resource\Item::class, $resource);
    }

    /** @test */
    public function includeVendor_returns_null_when_vendor_missing()
    {
        $expense = RecurringExpense::factory()->make();

        $expense->setRelation('vendor', null);

        $transformer = new RecurringExpenseTransformer();

        $this->assertNull($transformer->includeVendor($expense));
    }
}

