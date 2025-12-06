<?php

/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2025. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace App\Jobs\Client;

use App\DataProviders\USStates;
use App\Libraries\MultiDB;
use App\Models\Client;
use App\Models\Company;
use App\Models\Location;
use App\Utils\Traits\MakesHash;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class UpdateLocationTaxData implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use MakesHash;

    public $tries = 1;

    /**
     * Create a new job instance.
     *
     * @param Location $location
     * @param Company $company
     */
    public function __construct(public Location $location, protected Company $company)
    {
    }

    public function middleware()
    {
        return [(new WithoutOverlapping($this->location->client->id.$this->company->company_key))->releaseAfter(60)];
    }

    public function failed($exception)
    {
        nlog("UpdateLocationTaxData failed => ".$exception->getMessage());
        config(['queue.failed.driver' => null]);

    }

}
