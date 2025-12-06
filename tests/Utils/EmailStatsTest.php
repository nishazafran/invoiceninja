<?php

namespace Tests\Utils;

use App\Utils\EmailStats;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;

class EmailStatsTest extends TestCase
{
     public function test_inc_increments_cache()
    {
        Cache::shouldReceive('increment')
            ->once()
            ->with('email_quotaemail_test_company');

        EmailStats::inc('test_company');
    }
    
    public function test_get_cache_value()
    {
        Cache::shouldReceive('get')
            ->once()
            ->with('email_'.$companyKey = 'test_company')
            ->andReturn(5);

        $count = EmailStats::count($companyKey);

        $this->assertEquals(5, $count);
    }

    public function test_forgets_cache()
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with('email_'.$companyKey = 'test_company');

        EmailStats::clear($companyKey);
    }

    public function test_clearCompanies()
    {
        $company1 = new Company();
        $company1->company_key = 'c1';

        $company2 = new Company();
        $company2->company_key = 'c2';

        $companies = new Collection([$company1, $company2]);

        Cache::shouldReceive('forget')
            ->once()
            ->with('email_c1');
        Cache::shouldReceive('forget')
            ->once()
            ->with('email_c2');

        EmailStats::clearCompanies($companies);
    }
}

