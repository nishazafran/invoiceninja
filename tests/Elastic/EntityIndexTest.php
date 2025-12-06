<?php

namespace Tests\Elastic;

use PHPUnit\Framework\TestCase;
use App\Elastic\Index\EntityIndex;
use Elastic\Migrations\Facades\Index;
use Mockery;

class EntityIndexTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_calls_createRaw_with_correct_arguments()
    {
        // Arrange
        $indexName = 'test_index';

        // Mock the facade
        Index::shouldReceive('createRaw')
            ->once()
            ->with($indexName, Mockery::type('array'));

        $entityIndex = new EntityIndex();

        // Act
        $entityIndex->create($indexName);

        // Assert — passing means createRaw() was called correctly
        $this->assertTrue(true);
    }
}

