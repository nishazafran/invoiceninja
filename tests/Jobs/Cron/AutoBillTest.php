<?php

namespace Tests\Jobs\Cron;
use App\Models\Invitation;
use App\Models\Account;
use App\Models\User;
use App\Jobs\Cron\AutoBill;
use App\Jobs\Entity\EmailEntity;
use App\Models\Invoice;
use App\Models\Client;
use App\Libraries\MultiDB;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Mockery;

class AutoBillTest extends TestCase
{
    use RefreshDatabase;


    public function test_handle_with_invoice_not_found()
    {
        $this->partialMock(Invoice::class, function ($mock) {
            $mock->shouldReceive('withTrashed->find')->andReturn(false);
        });

        $job = new AutoBill(9999, null, false);
        $job->handle();

        $this->assertTrue(true);
    }
}

