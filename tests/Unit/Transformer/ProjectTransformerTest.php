<?php

namespace Tests\Unit\Transformers;

use App\Models\Client;
use App\Models\Document;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Quote;
use App\Models\Task;
use App\Transformers\ProjectTransformer;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use Mockery;
use Tests\TestCase;

class ProjectTransformerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function makeProjectMock(): Project
    {
        $project = Mockery::mock(Project::class)->makePartial();

        $project->id = 1;
        $project->user_id = 2;
        $project->assigned_user_id = 3;
        $project->client_id = 4;
        $project->name = 'Test Project';
        $project->number = 'P-001';
        $project->created_at = time();
        $project->updated_at = time();
        $project->deleted_at = null;
        $project->is_deleted = false;
        $project->task_rate = 50.0;
        $project->due_date = '2025-12-31';
        $project->private_notes = 'Private';
        $project->public_notes = 'Public';
        $project->budgeted_hours = 100.0;
        $project->custom_value1 = 'CV1';
        $project->custom_value2 = 'CV2';
        $project->custom_value3 = 'CV3';
        $project->custom_value4 = 'CV4';
        $project->color = '#ff0000';
        $project->current_hours = 10;

        // Relations
        $project->client = Mockery::mock(Client::class);
        $project->documents = [Mockery::mock(Document::class)];
        $project->tasks = [Mockery::mock(Task::class)];
        $project->invoices = [Mockery::mock(Invoice::class)];
        $project->expenses = [Mockery::mock(Expense::class)];
        $project->quotes = [Mockery::mock(Quote::class)];

        return $project;
    }

    /** @test */
    public function transform_returns_correct_array()
    {
        $project = $this->makeProjectMock();

        $transformer = Mockery::mock(ProjectTransformer::class)->makePartial();
        $transformer->shouldAllowMockingProtectedMethods();
        $transformer->shouldReceive('encodePrimaryKey')->andReturnUsing(fn($id) => (string) $id);

        $data = $transformer->transform($project);

        $this->assertIsArray($data);
        $this->assertEquals('1', $data['id']);
        $this->assertEquals('2', $data['user_id']);
        $this->assertEquals('3', $data['assigned_user_id']);
        $this->assertEquals('4', $data['client_id']);
        $this->assertEquals('Test Project', $data['name']);
        $this->assertEquals('P-001', $data['number']);
        $this->assertEquals('Private', $data['private_notes']);
        $this->assertEquals('Public', $data['public_notes']);
    }

    /** @test */
    public function include_documents_returns_collection()
    {
        $project = $this->makeProjectMock();
        $transformer = new ProjectTransformer();

        $resource = $transformer->includeDocuments($project);

        $this->assertInstanceOf(Collection::class, $resource);
        $this->assertCount(1, $resource->getData());
    }

    /** @test */
    public function include_client_returns_item_or_null()
    {
        $project = $this->makeProjectMock();

        $transformer = Mockery::mock(ProjectTransformer::class)->makePartial();
        $transformer->shouldAllowMockingProtectedMethods();
        $transformer->shouldReceive('encodePrimaryKey')->andReturnUsing(fn($id) => (string) $id);

        // Client exists
        $resource = $transformer->includeClient($project);
        $this->assertInstanceOf(Item::class, $resource);

        // Client is null
        $project->client = null;
        $resource = $transformer->includeClient($project);
        $this->assertNull($resource);
    }

    /** @test */
    public function include_tasks_invoices_expenses_quotes_return_collections()
    {
        $project = $this->makeProjectMock();
        $transformer = new ProjectTransformer();

        $methods = [
            'includeTasks',
            'includeInvoices',
            'includeExpenses',
            'includeQuotes'
        ];

        foreach ($methods as $method) {
            $resource = $transformer->$method($project);
            $this->assertInstanceOf(Collection::class, $resource);
        }

        // Test empty relations return empty collections
        $project->tasks = [];
        $project->invoices = [];
        $project->expenses = [];
        $project->quotes = [];

        foreach ($methods as $method) {
            $resource = $transformer->$method($project);
            $this->assertInstanceOf(Collection::class, $resource);
            $this->assertEmpty($resource->getData());
        }
    }
}

