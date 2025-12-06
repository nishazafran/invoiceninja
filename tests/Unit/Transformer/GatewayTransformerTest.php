<?php

namespace Tests\Unit\Transformers;

use App\Models\Gateway;
use App\Transformers\GatewayTransformer;
use Mockery;
use PHPUnit\Framework\TestCase;

class GatewayTransformerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testTransform()
    {
        // Create a partial mock of Gateway
        $gateway = Mockery::mock(Gateway::class)->makePartial();
        $gateway->id = 1;
        $gateway->name = 'Test Gateway';
        $gateway->key = 'test_key';
        $gateway->provider = 'stripe';
        $gateway->visible = true;
        $gateway->sort_order = 5;
        $gateway->default_gateway_type_id = 2;
        $gateway->site_url = 'https://example.com';
        $gateway->is_offsite = false;
        $gateway->is_secure = true;
        $gateway->fields = '{}';
        $gateway->updated_at = time();
        $gateway->created_at = time();

        // Stub getMethods() method
        $gateway->shouldReceive('getMethods')->andReturn(['credit_card', 'paypal']);

        // Create a partial mock of transformer to mock encodePrimaryKey
        $transformer = Mockery::mock(GatewayTransformer::class)->makePartial();
        $transformer->shouldAllowMockingProtectedMethods();
        $transformer->shouldReceive('encodePrimaryKey')->with(1)->andReturn('encoded_1');

        $result = $transformer->transform($gateway);

        $this->assertIsArray($result);
        $this->assertEquals('encoded_1', $result['id']);
        $this->assertEquals('Test Gateway', $result['name']);
        $this->assertEquals('test_key', $result['key']);
        $this->assertEquals('stripe', $result['provider']);
        $this->assertTrue($result['visible']);
        $this->assertEquals(5, $result['sort_order']);
        $this->assertEquals('2', $result['default_gateway_type_id']);
        $this->assertEquals('https://example.com', $result['site_url']);
        $this->assertFalse($result['is_offsite']);
        $this->assertTrue($result['is_secure']);
        $this->assertEquals('{}', $result['fields']);
        $this->assertIsInt($result['updated_at']);
        $this->assertIsInt($result['created_at']);
        $this->assertEquals(['credit_card', 'paypal'], $result['options']);
    }
}

