<?php

namespace Tests\Unit\Transformers;

use App\Models\Activity;
use App\Models\Backup;
use App\Transformers\PurchaseOrderHistoryTransformer;
use App\Transformers\ActivityTransformer;
use League\Fractal\Resource\Item;
use Mockery;
use PHPUnit\Framework\TestCase;

class PurchaseOrderHistoryTransformerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function makeBackupMock(): Backup
    {
        $backup = Mockery::mock(Backup::class)->makePartial();

        $backup->id = 1;
        $backup->activity_id = 2;
        $backup->amount = 100.0;
        $backup->created_at = time();
        $backup->updated_at = time();

        // Relations
        $backup->activity = Mockery::mock(Activity::class);

        return $backup;
    }

    /** @test */
    public function transform_returns_correct_array_for_valid_backup()
    {
        $backup = $this->makeBackupMock();
        $transformer = Mockery::mock(PurchaseOrderHistoryTransformer::class)->makePartial();
        $transformer->shouldAllowMockingProtectedMethods();
        $transformer->shouldReceive('encodePrimaryKey')->andReturnUsing(fn($id) => (string) $id);

        $data = $transformer->transform($backup);

        $this->assertIsArray($data);
        $this->assertEquals('1', $data['id']);
        $this->assertEquals('2', $data['activity_id']);
        $this->assertEquals('', $data['json_backup']);  // matches transformer behavior
        $this->assertEquals('', $data['html_backup']);  // matches transformer behavior
        $this->assertEquals(100.0, $data['amount']);
        $this->assertIsInt($data['created_at']);
        $this->assertIsInt($data['updated_at']);
    }

    /** @test */
    public function transform_returns_default_array_for_null_backup()
    {
        $transformer = new PurchaseOrderHistoryTransformer();

        $data = $transformer->transform(null);

        $this->assertEquals('', $data['id']);
        $this->assertEquals('', $data['activity_id']);
        $this->assertEquals('', $data['json_backup']);
        $this->assertEquals('', $data['html_backup']);
        $this->assertEquals(0.0, $data['amount']);
        $this->assertEquals(0, $data['created_at']);
        $this->assertEquals(0, $data['updated_at']);
    }

    /** @test */
    public function include_activity_returns_item_when_activity_exists()
    {
        $backup = $this->makeBackupMock();
        $transformer = new PurchaseOrderHistoryTransformer();

        $resource = $transformer->includeActivity($backup);

        $this->assertInstanceOf(Item::class, $resource);
        $this->assertNotNull($resource->getData());
    }

    /** @test */
    public function include_activity_returns_item_with_null_data_when_activity_missing()
    {
        $backup = $this->makeBackupMock();
        $backup->activity = null;

        $transformer = new PurchaseOrderHistoryTransformer();
        $resource = $transformer->includeActivity($backup);

        $this->assertInstanceOf(Item::class, $resource);
        $this->assertNull($resource->getData()); // Fractal returns Item object even for null relation
    }
}

