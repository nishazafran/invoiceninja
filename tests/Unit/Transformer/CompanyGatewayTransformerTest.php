<?php

namespace Tests\Unit\Transformer;

use App\Transformers\EntityTransformer;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Mockery;
use PHPUnit\Framework\TestCase;

class EntityTransformerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testConstructAndGetDefaultIncludes()
    {
        $transformer = new EntityTransformer('json');
        $this->assertInstanceOf(EntityTransformer::class, $transformer);
        $this->assertIsArray($transformer->getDefaultIncludes());
        $this->assertEmpty($transformer->getDefaultIncludes());
    }

    public function testIncludeCollectionReturnsCollection()
    {
        $data = ['foo', 'bar'];

        // Partial mock to override collection method
        $transformer = Mockery::mock(EntityTransformer::class, ['json'])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $transformer->shouldReceive('collection')
            ->once()
            ->with($data, 'transformer', 'Entity')
            ->andReturn('mocked_collection');

        $result = $transformer->includeCollection($data, 'transformer', 'Entity');

        $this->assertEquals('mocked_collection', $result);
    }

    public function testIncludeCollectionWithNonJsonSerializer()
    {
        $data = ['foo', 'bar'];

        $transformer = Mockery::mock(EntityTransformer::class, ['array'])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $transformer->shouldReceive('collection')
            ->once()
            ->with($data, 'transformer', null) // entityType null because serializer != json
            ->andReturn('mocked_collection');

        $result = $transformer->includeCollection($data, 'transformer', 'Entity');

        $this->assertEquals('mocked_collection', $result);
    }

    public function testIncludeItemReturnsItem()
    {
        $data = ['foo'];

        $transformer = Mockery::mock(EntityTransformer::class, ['json'])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $transformer->shouldReceive('item')
            ->once()
            ->with($data, 'transformer', 'Entity')
            ->andReturn('mocked_item');

        $result = $transformer->includeItem($data, 'transformer', 'Entity');

        $this->assertEquals('mocked_item', $result);
    }

    public function testIncludeItemWithNonJsonSerializer()
    {
        $data = ['foo'];

        $transformer = Mockery::mock(EntityTransformer::class, ['array'])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $transformer->shouldReceive('item')
            ->once()
            ->with($data, 'transformer', null) // entityType null because serializer != json
            ->andReturn('mocked_item');

        $result = $transformer->includeItem($data, 'transformer', 'Entity');

        $this->assertEquals('mocked_item', $result);
    }

    public function testGetDefaultsReturnsNull()
    {
        $transformer = new EntityTransformer();
        $this->assertNull($transformer->getDefaults('entity'));
    }
}

