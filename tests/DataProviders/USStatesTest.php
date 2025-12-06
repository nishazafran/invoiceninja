<?php

namespace Tests\DataProviders;

use Tests\TestCase;
use App\DataProviders\USStates;
use Illuminate\Support\Facades\Http;
//use Illuminate\Support\Facades\Facade;

class USStatesTest extends TestCase
{

    public function test_returns_all_states()
    {
        $states = USStates::get();

        $this->assertIsArray($states);
        $this->assertArrayHasKey('AK', $states);
    }


    public function test_returns_state_from_direct_zip_map()
    {
        $state = USStates::getState('90210');
        $this->assertEquals('CA', $state);
    }


    public function test_returns_state_from_three_digit_prefix()
    {
        // 995xx always maps to AK
        $state = USStates::getState('99501');
        $this->assertEquals('AK', $state);
    }

    /** @test */
    public function test_returns_false_when_three_digit_prefix_is_dash_dash()
    {
        // prefix “000” returns “--” in the provider → false
        $result = USStates::getStateFromThreeDigitPrefix('00000');
        $this->assertFalse($result);
    }

    /** @test */
    public function test_returns_state_from_zippo_api_when_prefix_and_map_fail()
    {
        Http::fake([
            'https://api.zippopotam.us/us/12345' => Http::response([
                "post code" => "12345",
                "country" => "United States",
                "places" => [
                    [
                        "state abbreviation" => "NY",
                    ]
                ]
            ], 200),
        ]);

    	$state = \App\DataProviders\USStates::getState('12345');

        $this->assertEquals('NY', $state);
    }

    /** @test */
    public function test_returns_false_when_zippo_api_fails()
    {
        Http::fake([
            'https://api.zippopotam.us/us/98700' => Http::response([], 500),
        ]);

        $result = USStates::getStateFromZippo('98700');
        $this->assertFalse($result); // covers response->failed()
    }

    /** @test */
    public function test_returns_false_when_zippo_returns_no_places()
    {
        Http::fake([
            'https://api.zippopotam.us/us/09999' => Http::response([
                "post code" => "09999",
                "places" => []
            ], 200),
        ]);

        $result = USStates::getStateFromZippo('09999');
        $this->assertFalse($result); // covers isset($data->places[0]) false
    }

    /** @test */
    public function test_throws_exception_when_no_map_prefix_or_api_match()
    {
        Http::fake([
            'https://api.zippopotam.us/us/00001' => Http::response([], 500),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Zip code not found');

        USStates::getState('00001');
    }

    public function test_returns_state_abbreviation_from_zippo_api()
    {
    	Http::fake([
    	    'https://api.zippopotam.us/us/12345' => Http::response([
    	        "post code" => "12345",
    	        "country" => "United States",
    	        "places" => [
    	            [
    	                "place name" => "Schenectady",
    	                "longitude" => "-73.9587",
    	                "state" => "New York",
    	                "state abbreviation" => "NY",
    	                "latitude" => "42.8143"
    	            ]
    	        ]
    	    ], 200),
    	]);

    	$result = \App\DataProviders\USStates::getStateFromZippo('12345');
	
    	// This verifies that the exact `return $data->places[0]->{'state abbreviation'};` executed
    	$this->assertSame('NY', $result);
    }

}

