<?php

namespace Tests\Observers;

use Tests\TestCase;
use App\Models\Location;
use App\Models\Company;
use App\Models\Account;
use App\Observers\LocationObserver;
use App\Jobs\Client\UpdateLocationTaxData;
use Illuminate\Support\Facades\Queue;

class LocationObserverTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    /** @test */
    public function it_dispatches_job_on_created_if_conditions_met()
    {
        $account = Account::factory()->make(['is_free_hosted_client' => false]);
        $company = Company::factory()->make([
            'calculate_taxes' => true,
            'account' => $account
        ]);

        $location = Location::factory()->make([
            'country_id' => 840,
            'company' => $company
        ]);

        $observer = new LocationObserver();
        $observer->created($location);

        Queue::assertPushed(UpdateLocationTaxData::class, function ($job) use ($location, $company) {
            return $job->location === $location && $job->company === $company;
        });
    }

    /** @test */
    public function it_dispatches_job_on_updated_when_postal_code_changes_and_conditions_met()
    {
        $account = Account::factory()->make(['is_free_hosted_client' => false]);
        $company = Company::factory()->make([
            'calculate_taxes' => true,
            'account' => $account
        ]);

        $location = Location::factory()->make([
            'country_id' => 840,
            'postal_code' => '12345',
            'company' => $company
        ]);

        // Simulate original postal_code
        $location->setRawOriginal('postal_code', '00000');

        $observer = new LocationObserver();
        $observer->updated($location);

        Queue::assertPushed(UpdateLocationTaxData::class);
    }

    /** @test */
    public function deleted_restored_and_forceDeleted_do_not_dispatch_any_jobs()
    {
        $account = Account::factory()->make(['is_free_hosted_client' => false]);
        $company = Company::factory()->make([
            'calculate_taxes' => true,
            'account' => $account
        ]);

        $location = Location::factory()->make([
            'country_id' => 840,
            'company' => $company
        ]);

        $observer = new LocationObserver();
        $observer->deleted($location);
        $observer->restored($location);
        $observer->forceDeleted($location);

        Queue::assertNothingPushed();
    }
}
