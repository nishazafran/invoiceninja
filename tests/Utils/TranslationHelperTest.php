<?php

namespace Tests\Utils;

use App\Utils\TranslationHelper;
use Illuminate\Support\Collection;
use Tests\TestCase;

class TranslationHelperTest extends TestCase
{
    public function test_getCountries_returns_collection_from_app()
    {
        $mockCountries = collect([
            (object)['id' => 1, 'name' => 'Country1'],
            (object)['id' => 2, 'name' => 'Country2'],
        ]);

        // Mock the app() helper to return our mock collection
        $this->mockApp('countries', $mockCountries);

        $countries = TranslationHelper::getCountries();

        $this->assertInstanceOf(Collection::class, $countries);
        $this->assertCount(2, $countries);
        $this->assertEquals('Country1', $countries[0]->name);
        $this->assertEquals('Country2', $countries[1]->name);
    }

    public function test_getCurrencies_returns_collection_from_app()
    {
        $mockCurrencies = collect([
            (object)['id' => 1, 'name' => 'USD'],
            (object)['id' => 2, 'name' => 'EUR'],
        ]);

        $this->mockApp('currencies', $mockCurrencies);

        $currencies = TranslationHelper::getCurrencies();

        $this->assertInstanceOf(Collection::class, $currencies);
        $this->assertCount(2, $currencies);
        $this->assertEquals('USD', $currencies[0]->name);
        $this->assertEquals('EUR', $currencies[1]->name);
    }

    /**
     * Helper to mock the app() helper return values
     */
    private function mockApp(string $key, $return)
    {
        $app = $this->createMock(\Illuminate\Contracts\Foundation\Application::class);
        $app->method('make')->with($key)->willReturn($return);
        $this->app->instance($key, $return);
    }
}

