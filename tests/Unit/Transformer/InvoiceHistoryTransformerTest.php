<?php

namespace Tests\Unit\Transformers;

use App\Models\Activity;
use App\Models\Backup;
use App\Transformers\ActivityTransformer;
use App\Transformers\InvoiceHistoryTransformer;
use League\Fractal\Resource\Item as FractalItem;
use Mockery;
use PHPUnit\Framework\TestCase;

class InvoiceHistoryTransformerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_transform_null_backup()
    {
        $transformer = new InvoiceHistoryTransformer();

        $result = $transformer->transform(null);

        $this->assertEquals([
            'id' => '',
            'activity_id' => '',
            'json_backup' => '',
            'html_backup' => '',
            'amount' => 0.0,
            'created_at' => 0,
            'updated_at' => 0,
        ], $result);
    }

    /** @test */
    public function it_can_transform_backup_object()
    {
        // Use a real Backup instance
        $backup = new Backup();
        $backup->id = 1;
        $backup->activity_id = 2;
        $backup->amount = 100.5;
        $backup->created_at = 1670000000;
        $backup->updated_at = 1670000100;

        $transformer = Mockery::mock(InvoiceHistoryTransformer::class)->makePartial();
        $transformer->shouldAllowMockingProtectedMethods();
        $transformer->shouldReceive('encodePrimaryKey')->with(1)->andReturn('encoded_1');
        $transformer->shouldReceive('encodePrimaryKey')->with(2)->andReturn('encoded_2');

        $result = $transformer->transform($backup);

        $this->assertEquals([
            'id' => 'encoded_1',
            'activity_id' => 'encoded_2',
            'json_backup' => '',
            'html_backup' => '',
            'amount' => 100.5,
            'created_at' => 1670000000,
            'updated_at' => 1670000100,
        ], $result);
    }

    /** @test */
    public function it_can_include_activity()
    {
        $activity = new Activity();
        $backup = new Backup();
        $backup->activity = $activity;

        $transformer = Mockery::mock(InvoiceHistoryTransformer::class)->makePartial();
        $transformer->shouldAllowMockingProtectedMethods();

        $fractalItem = Mockery::mock(FractalItem::class);

        $transformer->shouldReceive('includeItem')
            ->once()
            ->with($activity, Mockery::type(ActivityTransformer::class), Activity::class)
            ->andReturn($fractalItem);

        $result = $transformer->includeActivity($backup);

        $this->assertSame($fractalItem, $result);
    }
}

