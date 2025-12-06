<?php

namespace Tests\Utils\ClientPortal\CustomMessage;

use Tests\TestCase;
use App\Utils\ClientPortal\CustomMessage\CustomMessageFacade;
use Illuminate\Support\Facades\Facade;
use Mockery;

class CustomMessageFacadeTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_returns_the_correct_facade_accessor()
    {
        // Use reflection to access the protected method
        $method = new \ReflectionMethod(CustomMessageFacade::class, 'getFacadeAccessor');
        $method->setAccessible(true);

        $this->assertEquals(
            'customMessage',
            $method->invoke(null)
        );
    }

    public function test_forwards_calls_to_the_underlying_class()
    {
        // Create mock for the underlying service
        $mock = Mockery::mock();
        $mock->shouldReceive('generate')
             ->once()
             ->with('hello')
             ->andReturn('processed_hello');

        // Bind mock into Laravel service container
        $this->app->instance('customMessage', $mock);

        // Facade call
        $result = CustomMessageFacade::generate('hello');

        $this->assertEquals('processed_hello', $result);
    }
}

