<?php

namespace Tests\Observers;

use Tests\TestCase;
use App\Models\Expense;
use App\Models\Webhook;
use App\Observers\ExpenseObserver;
use App\Jobs\Util\WebhookHandler;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Bus;
use Mockery;

class ExpenseObserverTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
        Bus::fake(); // in case the job uses Bus
    }

    /** @test */
    public function test_dispatches_create_webhook_when_subscribed()
    {
        $expense = Expense::factory()->make();

        // Mock Webhook::where()->where()->exists() to return true
        $mock = Mockery::mock('alias:' . Webhook::class);
        $mock->shouldReceive('where->where->exists')->andReturn(true);

        $observer = new ExpenseObserver();
        $observer->created($expense);

        Queue::assertPushed(WebhookHandler::class, function ($job) use ($expense) {
            return $job->object === $expense;
        });
    }

    /** @test */
    public function test_dispatches_update_restore_delete_webhooks()
    {
        $expense = Expense::factory()->make([
            'is_deleted' => false,
            'deleted_at' => null,
        ]);

        // Mock Webhook
        $mock = Mockery::mock('alias:' . Webhook::class);
        $mock->shouldReceive('where->where->exists')->andReturn(true);

        $observer = new ExpenseObserver();

        // Normal update
        $observer->updated($expense);
        Queue::assertPushed(WebhookHandler::class);

        // Simulate restore
        $expense->setRawOriginal('deleted_at', now());
        $expense->deleted_at = null;
        $observer->updated($expense);
        Queue::assertPushed(WebhookHandler::class);

        // Simulate delete
        $expense->is_deleted = true;
        $observer->updated($expense);
        Queue::assertPushed(WebhookHandler::class);
    }

    /** @test */
    public function test_dispatches_archive_webhook_on_deleted_if_not_already_deleted()
    {
        $expense = Expense::factory()->make(['is_deleted' => false]);

        // Mock Webhook
        $mock = Mockery::mock('alias:' . Webhook::class);
        $mock->shouldReceive('where->where->exists')->andReturn(true);

        $observer = new ExpenseObserver();
        $observer->deleted($expense);

        Queue::assertPushed(WebhookHandler::class);
    }

    /** @test */
    public function test_skips_dispatch_when_deleted_already_true()
    {
        $expense = Expense::factory()->make(['is_deleted' => true]);

        $observer = new ExpenseObserver();
        $observer->deleted($expense);

        Queue::assertNothingPushed();
    }

    /** @test */
    public function test_restored_and_forceDeleted_do_nothing()
    {
        $expense = Expense::factory()->make();

        $observer = new ExpenseObserver();
        $observer->restored($expense);
        $observer->forceDeleted($expense);

        Queue::assertNothingPushed();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

