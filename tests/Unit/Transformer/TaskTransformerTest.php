<?php

namespace Tests\Unit\Transformers;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\Document;
use App\Models\TaskStatus;
use App\Transformers\TaskTransformer;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;

class TaskTransformerTest extends TestCase
{
    /** @test */
    public function it_includes_all_related_entities()
    {
        // Create a mock task
        $task = new Task();
        
        // Assign related entities
        $task->documents = [new Document()];
        $task->invoice = new Invoice();
        $task->user = new User();
        $task->assigned_user = new User();
        $task->client = new Client();
        $task->status = new TaskStatus();
        $task->project = new Project();

        $transformer = new TaskTransformer();

        // Test include methods
        $this->assertInstanceOf(Collection::class, $transformer->includeDocuments($task));
        $this->assertInstanceOf(Item::class, $transformer->includeInvoice($task));
        $this->assertInstanceOf(Item::class, $transformer->includeUser($task));
        $this->assertInstanceOf(Item::class, $transformer->includeAssignedUser($task));
        $this->assertInstanceOf(Item::class, $transformer->includeClient($task));
        $this->assertInstanceOf(Item::class, $transformer->includeStatus($task));
        $this->assertInstanceOf(Item::class, $transformer->includeProject($task));
    }

    /** @test */
    public function it_returns_null_for_missing_related_entities()
    {
        $task = new Task(); // empty task, no relations

        $transformer = new TaskTransformer();

        $this->assertNull($transformer->includeInvoice($task));
        $this->assertNull($transformer->includeUser($task));
        $this->assertNull($transformer->includeAssignedUser($task));
        $this->assertNull($transformer->includeClient($task));
        $this->assertNull($transformer->includeStatus($task));
        $this->assertNull($transformer->includeProject($task));
    }

    /** @test */
    public function it_transforms_task_with_all_fields()
    {
        $task = new Task();
        $task->id = 1;
        $task->user_id = 2;
        $task->assigned_user_id = 3;
        $task->number = 'T-100';
        $task->description = 'Test Task';
        $task->duration = 5;
        $task->rate = 100;
        $task->created_at = time();
        $task->updated_at = time();
        $task->deleted_at = null;
        $task->invoice_id = 4;
        $task->client_id = 5;
        $task->project_id = 6;
        $task->is_deleted = false;
        $task->time_log = '2h';
        $task->is_running = true;
        $task->custom_value1 = 'CV1';
        $task->custom_value2 = 'CV2';
        $task->custom_value3 = 'CV3';
        $task->custom_value4 = 'CV4';
        $task->status_id = 7;
        $task->status_sort_order = 1;
        $task->is_date_based = true;
        $task->status_order = 2;
        $task->calculated_start_date = '2025-12-06';

        $transformer = new TaskTransformer();
        $data = $transformer->transform($task);

        $this->assertEquals($transformer->encodePrimaryKey(1), $data['id']);
        $this->assertEquals('T-100', $data['number']);
        $this->assertEquals('Test Task', $data['description']);
        $this->assertEquals(5, $data['duration']);
        $this->assertEquals(100, $data['rate']);
        $this->assertEquals('2h', $data['time_log']);
        $this->assertEquals('CV1', $data['custom_value1']);
        $this->assertEquals('CV2', $data['custom_value2']);
        $this->assertEquals('CV3', $data['custom_value3']);
        $this->assertEquals('CV4', $data['custom_value4']);
        $this->assertTrue($data['is_running']);
        $this->assertEquals('2025-12-06', $data['date']);
    }
}

