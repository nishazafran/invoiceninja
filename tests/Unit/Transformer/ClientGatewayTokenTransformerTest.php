<?php

namespace Tests\Unit\Transformers;

use App\Models\ClientGatewayToken;
use App\Transformers\ClientGatewayTokenTransformer;
use Tests\TestCase;
use stdClass;

class ClientGatewayTokenTransformerTest extends TestCase
{
    private ClientGatewayTokenTransformer $transformer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transformer = new ClientGatewayTokenTransformer();
    }

    /** @test */
    public function test_transform_returns_correct_array()
    {
        $meta = new stdClass();
        $meta->exp_month = 12;
        $meta->exp_year = 2030;
        $meta->brand = 'Visa';
        $meta->last4 = '4242';
        $meta->type = 1;

        $cgt = new ClientGatewayToken();
        $cgt->id = 1;
        $cgt->token = 'tok_123';
        $cgt->gateway_customer_reference = 'cust_123';
        $cgt->gateway_type_id = 5;
        $cgt->company_gateway_id = 10;
        $cgt->is_default = true;
        $cgt->meta = $meta;
        $cgt->created_at = 1699999999;
        $cgt->updated_at = 1700000000;
        $cgt->deleted_at = null;
        $cgt->is_deleted = false;

        $result = $this->transformer->transform($cgt);

        $this->assertEquals($this->transformer->encodePrimaryKey(1), $result['id']);
        $this->assertEquals('tok_123', $result['token']);
        $this->assertEquals('cust_123', $result['gateway_customer_reference']);
        $this->assertEquals('5', $result['gateway_type_id']);
        $this->assertEquals($this->transformer->encodePrimaryKey(10), $result['company_gateway_id']);
        $this->assertTrue($result['is_default']);
        $this->assertEquals('12', $result['meta']->exp_month);
        $this->assertEquals('2030', $result['meta']->exp_year);
        $this->assertEquals('Visa', $result['meta']->brand);
        $this->assertEquals('4242', $result['meta']->last4);
        $this->assertEquals(1, $result['meta']->type);
        $this->assertEquals(1699999999, $result['created_at']);
        $this->assertEquals(1700000000, $result['updated_at']);
        $this->assertEquals(0, $result['archived_at']); // deleted_at is null
        $this->assertFalse($result['is_deleted']);
    }

    /** @test */
    public function test_transform_handles_empty_values()
    {
        $cgt = new ClientGatewayToken();
        $cgt->id = 2;
        $cgt->token = null;
        $cgt->gateway_customer_reference = null;
        $cgt->gateway_type_id = null;
        $cgt->company_gateway_id = null;
        $cgt->is_default = null;
        $cgt->meta = new stdClass();
        $cgt->created_at = null;
        $cgt->updated_at = null;
        $cgt->deleted_at = null;
        $cgt->is_deleted = null;

        $result = $this->transformer->transform($cgt);

        $this->assertEquals($this->transformer->encodePrimaryKey(2), $result['id']);
        $this->assertEquals('', $result['token']);
        $this->assertEquals('', $result['gateway_customer_reference']);
        $this->assertEquals('', $result['gateway_type_id']);
        $this->assertEquals('', $result['company_gateway_id']);
        $this->assertFalse($result['is_default']);
        $this->assertEquals(new stdClass(), $result['meta']);
        $this->assertEquals(0, $result['created_at']);
        $this->assertEquals(0, $result['updated_at']);
        $this->assertEquals(0, $result['archived_at']);
        $this->assertFalse($result['is_deleted']);
    }

    /** @test */
    public function test_type_cast_meta_only_casts_existing_properties()
    {
        $meta = new stdClass();
        $meta->exp_month = 5;
        $meta->brand = 'MasterCard';

        $method = new \ReflectionMethod(ClientGatewayTokenTransformer::class, 'typeCastMeta');
        $method->setAccessible(true);

        $casted = $method->invoke($this->transformer, $meta);

        $this->assertEquals('5', $casted->exp_month);
        $this->assertEquals('MasterCard', $casted->brand);
        $this->assertObjectNotHasAttribute('exp_year', $casted);
        $this->assertObjectNotHasAttribute('last4', $casted);
        $this->assertObjectNotHasAttribute('type', $casted);
    }
}

