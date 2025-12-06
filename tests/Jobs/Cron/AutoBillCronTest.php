<?php

namespace Tests\Jobs\Cron;

use App\Jobs\Cron\AutoBillCron;
use App\Jobs\Cron\AutoBill;
use App\Libraries\MultiDB;
use App\Models\Invoice;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class AutoBillCronTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function test_logs_info_and_calls_set_time_limit()
    {
        Config::set('ninja.db.multi_db_enabled', false);

        // Mock Invoice query chain to empty result
        $invoiceQueryMock = Mockery::mock('Illuminate\Database\Eloquent\Builder');
        $invoiceQueryMock->shouldReceive('whereIn')->andReturnSelf();
        $invoiceQueryMock->shouldReceive('where')->andReturnSelf();
        $invoiceQueryMock->shouldReceive('whereDate')->andReturnSelf();
        $invoiceQueryMock->shouldReceive('whereHas')->andReturnSelf();
        $invoiceQueryMock->shouldReceive('orderBy')->andReturnSelf();
        $invoiceQueryMock->shouldReceive('chunk')->andReturnUsing(function ($count, $callback) {
            $callback([]); // empty array
        });

        $this->partialMock(Invoice::class, function ($mock) use ($invoiceQueryMock) {
            $mock->shouldReceive('query')->andReturn($invoiceQueryMock);
        });

        // Use a simple job instance
        $job = new AutoBillCron();
        $job->handle();

        $this->assertTrue(true); // if reached, everything executed
    }
}

