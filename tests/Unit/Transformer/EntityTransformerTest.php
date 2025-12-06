<?php

namespace Tests\Unit\Transformers;

use App\Transformers\EntityTransformer;
use League\Fractal\Resource\Collection as FractalCollection;
use League\Fractal\Resource\Item as FractalItem;
use Mockery;
use PHPUnit\Framework\TestCase;

class EntityTransformerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_construct_with_serializer()
    {
        $transformer = new EntityTransformer('array');
        $this->assertInstanceOf(EntityTransformer::class, $transformer);
    }

    /** @test */
    public function it_can_include_collection_with_json_serializer()
    {
        $data = collect([1, 2, 3]);
        $transformer = Mockery::mock(EntityTransformer::class)->makePartial();
        $transformer->shouldAllowMockingProtectedMethods();

        $fractalCollection = Mockery::mock(FractalCollection::class);

        $transformer->shouldReceive('collection')
            ->once()
            ->with($data, 'MockTransformer', 'EntityClass')
            ->andReturn($fractalCollection);

        $result = $transformer->includeCollection($data, 'MockTransformer', 'EntityClass');
        $this->assertSame($fractalCollection, $result);
    }

    /** @test */
    public function it_can_include_collection_with_array_serializer_entity_type_null()
    {
        $data = collect([1, 2, 3]);
        $transformer = Mockery::mock(EntityTransformer::class, ['array'])->makePartial();
        $transformer->shouldAllowMockingProtectedMethods();

        $fractalCollection = Mockery::mock(FractalCollection::class);

        // Expect entityType null because serializer is 'array'
        $transformer->shouldReceive('collection')
            ->once()
            ->with($data, 'MockTransformer', null)
            ->andReturn($fractalCollection);

        $result = $transformer->includeCollection($data, 'MockTransformer', 'EntityClass');
        $this->assertSame($fractalCollection, $result);
    }

    /** @test */
    public function it_can_include_item_with_json_serializer()
    {
        $item = (object)['id' => 1];
        $transformer = Mockery::mock(EntityTransformer::class)->makePartial();
        $transformer->shouldAllowMockingProtectedMethods();

        $fractalItem = Mockery::mock(FractalItem::class);

        $transformer->shouldReceive('item')
            ->once()
            ->with($item, 'MockTransformer', 'EntityClass')
            ->andReturn($fractalItem);

        $result = $transformer->includeItem($item, 'MockTransformer', 'EntityClass');
        $this->assertSame($fractalItem, $result);
    }

    /** @test */
    public function it_can_include_item_with_array_serializer_entity_type_null()
    {
        $item = (object)['id' => 1];
        $transformer = Mockery::mock(EntityTransformer::class, ['array'])->makePartial();
        $transformer->shouldAllowMockingProtectedMethods();

        $fractalItem = Mockery::mock(FractalItem::class);

        // Expect entityType null because serializer is 'array'
        $transformer->shouldReceive('item')
            ->once()
            ->with($item, 'MockTransformer', null)
            ->andReturn($fractalItem);

        $result = $transformer->includeItem($item, 'MockTransformer', 'EntityClass');
        $this->assertSame($fractalItem, $result);
    }

    /** @test */
    public function it_can_get_default_includes()
    {
        $transformer = new EntityTransformer();

        $reflection = new \ReflectionClass($transformer);
        $property = $reflection->getProperty('defaultIncludes');
        $property->setAccessible(true);
        $property->setValue($transformer, ['include_test']);

        $this->assertEquals(['include_test'], $transformer->getDefaultIncludes());
    }

    /** @test */
    public function it_can_call_get_defaults()
    {
        $transformer = new EntityTransformer();
        $entity = (object)['any' => 'value'];

        $reflection = new \ReflectionClass($transformer);
        $method = $reflection->getMethod('getDefaults');
        $method->setAccessible(true);

        $this->assertNull($method->invoke($transformer, $entity));
    }
}

