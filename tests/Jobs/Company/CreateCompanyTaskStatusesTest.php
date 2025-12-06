<?php

namespace Tests\Jobs\Company;

use App\Jobs\Company\CreateCompanyTaskStatuses;
use App\Libraries\MultiDB;
use App\Models\TaskStatus;
use Illuminate\Support\Facades\App;
use Mockery;
use Tests\TestCase;

class CreateCompanyTaskStatusesTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_constructor_sets_company_and_user()
    {
        $company = (object) ['id' => 1, 'db' => 'db1', 'locale' => fn() => 'en'];
        $user = (object) ['id' => 2];

        $job = new CreateCompanyTaskStatuses($company, $user);

        $reflection = new \ReflectionClass($job);
        $companyProp = $reflection->getProperty('company');
        $companyProp->setAccessible(true);
        $userProp = $reflection->getProperty('user');
        $userProp->setAccessible(true);

        $this->assertSame($company, $companyProp->getValue($job));
        $this->assertSame($user, $userProp->getValue($job));
    }

    /** @test */
    public function test_handle_returns_if_task_statuses_exist()
    {
        $company = (object) ['id' => 1, 'db' => 'db1', 'locale' => fn() => 'en'];
        $user = (object) ['id' => 2];

        // Mock MultiDB
        Mockery::mock('alias:' . MultiDB::class)
            ->shouldReceive('setDb')
            ->once()
            ->with('db1');

        // Mock TaskStatus::where()->count()
        $taskStatusMock = Mockery::mock('alias:' . TaskStatus::class);
        $taskStatusMock->shouldReceive('where->count')->once()->andReturn(1);

        $job = new CreateCompanyTaskStatuses($company, $user);
        $result = $job->handle();

        $this->assertNull($result, 'Should return early if task statuses exist');
    }

   /** @test */
public function tets_handle_inserts_task_statuses_when_none_exist()
{
    // Mock company as an object with a locale() method
    $company = Mockery::mock();
    $company->id = 1;
    $company->db = 'db1';
    $company->shouldReceive('locale')->andReturn('en');

    // Mock user object
    $user = (object) ['id' => 2];

    // Mock MultiDB
    Mockery::mock('alias:' . MultiDB::class)
        ->shouldReceive('setDb')
        ->once()
        ->with('db1');

    // Mock TaskStatus static calls
    $taskStatusMock = Mockery::mock('alias:' . TaskStatus::class);
    $taskStatusMock->shouldReceive('where->count')
        ->once()
        ->andReturn(0);

    $taskStatusMock->shouldReceive('insert')
        ->once()
        ->withArgs(function ($taskStatuses) use ($company, $user) {
            // Ensure 4 statuses
            if (count($taskStatuses) !== 4) return false;

            $first = $taskStatuses[0];
            return $first['company_id'] === $company->id
                && $first['user_id'] === $user->id
                && isset($first['created_at'])
                && isset($first['updated_at'])
                && isset($first['status_order']);
        });

    // Mock App facade
    App::shouldReceive('forgetInstance')->once()->with('translator');
    App::shouldReceive('setLocale')->once()->with('en');

    // Run job
    $job = new CreateCompanyTaskStatuses($company, $user);
    $job->handle();

    // Simple assertion to satisfy PHPUnit
    $this->assertTrue(true);
}

}

