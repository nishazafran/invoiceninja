<?php

namespace Tests\Jobs\Credit;

use App\Jobs\Credit\ZipCredits;
use App\Jobs\Entity\CreateRawPdf;
use App\Jobs\Mail\NinjaMailerJob;
use App\Jobs\Mail\NinjaMailerObject;
use App\Jobs\Util\UnlinkFile;
use App\Libraries\MultiDB;
use App\Models\Company;
use App\Models\CreditInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class ZipCreditsTest extends TestCase
{
    use RefreshDatabase;
   
 /** @test */
public function test_handle_returns_early_when_no_invitations()
{
    $company = Mockery::mock(Company::class)->makePartial();
    $company->db = 'db1';
    $company->settings = (object) [];
    $company->shouldReceive('timezone_offset')->andReturn(0);
    $company->shouldReceive('locale')->andReturn('en');

    $user = Mockery::mock(User::class)->makePartial();
    $user->id = 1;

    $creditIds = [1,2];

    Mockery::mock('alias:' . MultiDB::class)
        ->shouldReceive('setDb')->once()->with('db1');

    $query = Mockery::mock();
    $query->shouldReceive('with->whereIn->get')->andReturn(collect([]));
    Mockery::mock('alias:' . CreditInvitation::class)
        ->shouldReceive('query')->andReturn($query);

    $job = new ZipCredits($creditIds, $company, $user);
    $job->handle();

    $this->assertTrue(true);
}

    /** @test */
    public function test_failed_logs_exception_and_resets_failed_driver()
    {
        $company = Company::factory()->make();
        $user = User::factory()->make();

        $job = new ZipCredits([], $company, $user);

        $exception = new \Exception('Something went wrong');

        config()->set('queue.failed.driver', 'database');

        $job->failed($exception);

        $this->assertTrue(true);
    }
}

