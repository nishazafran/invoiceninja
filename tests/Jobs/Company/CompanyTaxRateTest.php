<?php

namespace Tests\Jobs\Company;

use Tests\TestCase;
use App\Jobs\Company\CompanyTaxRate;
use App\Models\Company;
use App\Services\Tax\Providers\TaxProvider;
use App\DataProviders\USStates;
use App\DataMapper\Tax\ZipTax\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Account;
use Mockery;

class CompanyTaxRateTest extends TestCase
{

/** @test */
public function test_returns_if_state_cannot_be_determined()
{
    $account = Account::factory()->create();
    $company = Company::factory()->create([
        'account_id'=> $account->id,
        'db'=>'db-ninja'
    ]);

    $company->settings = (object)[
        'country_id'=>'840',
        'state'=>'',
        'postal_code'=>'',
        'city'=>''
    ];

    // MUST include seller_subregion=null to avoid undefined property access
    $company->tax_data = (object)[
        'seller_subregion' => null,
        'regions'=>(object)[
            'US'=>(object)[
                'subregions'=>(object)[]
            ]
        ]
    ];

    Mockery::mock('overload:App\Services\Tax\Providers\TaxProvider')
        ->shouldReceive('updateCompanyTaxData')
        ->shouldReceive('updatedTaxStatus')->andReturn(false);

    Mockery::mock('alias:App\DataProviders\USStates')
        ->shouldReceive('get')->andReturn([])
        ->shouldReceive('getState')->andReturn(false);

    (new CompanyTaxRate($company))->handle();

    // Returned before creating origin_tax_data
    $this->assertNull($company->origin_tax_data);
}


public function test_uses_seller_subregion_if_state_not_found()
{
    $account = Account::factory()->create();
    $company = Company::factory()->create([
        'account_id'=>$account->id,
        'db'=>'db-ninja'
    ]);

    $company->settings = (object)[
        'country_id'=>'840',
        'state'=>'',
        'postal_code'=>'00000',
        'city'=>'NY'
    ];

    $company->tax_data = (object)[
        'seller_subregion'=>'NY',
        'regions'=>(object)[
            'US'=>(object)[
                'subregions'=>(object)[
                    'NY'=>(object)['taxSales'=>3]
                ]
            ]
        ]
    ];

    Mockery::mock('overload:App\Services\Tax\Providers\TaxProvider')
        ->shouldReceive('updatedTaxStatus')->andReturn(false)
        ->shouldReceive('updateCompanyTaxData');

    // First USStates::get() executes, then getState throws
    Mockery::mock('alias:App\DataProviders\USStates')
        ->shouldReceive('get')->andReturn([])  // << required
        ->shouldReceive('getState')->andThrow(new \Exception());

    (new CompanyTaxRate($company))->handle();

    $this->assertEquals('NY',$company->origin_tax_data->geoState);
}


public function test_calculates_state_using_postal_code()
{
    $account = Account::factory()->create();
    $company = Company::factory()->create([
        'account_id'=> $account->id,
        'db'=>'db-ninja'
    ]);

    $company->settings = (object)[
        'country_id'=>'840',
        'state'=>'',
        'postal_code'=>'90001',
        'city'=>'LA'
    ];

    $company->tax_data = (object)[
        'regions'=>(object)[
            'US'=>(object)[
                'subregions'=>(object)[
                    'CA'=>(object)['taxSales'=>8]
                ]
            ]
        ]
    ];
    $company->origin_tax_data = null;

    Mockery::mock('alias:App\Libraries\MultiDB')
        ->shouldReceive('setDb');

    // Mock TaxProvider created inside handle()
    Mockery::mock('overload:App\Services\Tax\Providers\TaxProvider')
        ->shouldReceive('updatedTaxStatus')->andReturn(false)
        ->shouldReceive('updateCompanyTaxData');

    // MUST mock both get() and getState()
    Mockery::mock('alias:App\DataProviders\USStates')
        ->shouldReceive('get')->andReturn([])               // prevents error on line 58
        ->shouldReceive('getState')->with('90001')->andReturn('CA');

    (new CompanyTaxRate($company))->handle();

    $this->assertEquals('CA',$company->origin_tax_data->geoState);
}


    public function test_exits_if_tax_status_already_updated()
    {
        $account = Account::factory()->create();
        $company = Company::factory()->create([
            'account_id'=> $account->id,
            'db' => 'db-ninja'
        ]);

        $provider = Mockery::mock(TaxProvider::class, [$company]);
        $provider->shouldReceive('updatedTaxStatus')->andReturn(true);
        $this->app->instance(TaxProvider::class, $provider);

        (new CompanyTaxRate($company))->handle();

        $this->assertTrue(true); // if no exception, test passes
    }

    /** @test */
    public function test_uses_existing_state_when_valid()
    {
        $account = Account::factory()->create();
        $company = Company::factory()->create([
            'account_id'=> $account->id,
            'db' => 'db-ninja'
        ]);

        $company->settings = (object)[
            'country_id'=>'840',
            'state'=>'CA',
            'postal_code'=>'90001',
            'city'=>'LA'
        ];

        $company->tax_data = (object)[
            'regions'=>(object)[
                'US'=>(object)[
                    'subregions'=>(object)[
                        'CA'=>(object)['taxSales'=>5]
                    ]
                ]
            ]
        ];
        $company->origin_tax_data = null;

        $provider = Mockery::mock(TaxProvider::class,[$company]);
        $provider->shouldReceive('updatedTaxStatus')->andReturn(false);
        $provider->shouldReceive('updateCompanyTaxData');
        $this->app->instance(TaxProvider::class,$provider);

        Mockery::mock('alias:App\DataProviders\USStates')
            ->shouldReceive('get')->andReturn(['CA'=>'California']);

        (new CompanyTaxRate($company))->handle();

        $this->assertEquals('CA',$company->origin_tax_data->geoState);
    }

  
    /** @test */
    public function test_returns_middleware()
    {
        $company = Company::factory()->make();
        $job = new CompanyTaxRate($company);

        $this->assertNotEmpty($job->middleware());
    }

    /** @test */
    public function test_handles_failed()
    {
        $company = Company::factory()->make();
        $job = new CompanyTaxRate($company);

        $this->expectNotToPerformAssertions();
        $job->failed(new \Exception("fail"));
    }
}

