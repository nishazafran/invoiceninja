<?php

namespace Tests\Jobs\Document;

use Tests\TestCase;
use App\Models\Document;
use App\Jobs\Document\CopyDocs;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use App\Libraries\MultiDB;

class CopyDocsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_copies_documents_and_creates_new_entries()
    {
        // ---- MOCK STORAGE ----
        Storage::fake('local');

        // ---- FAKE COMPANY / USER ENTITY ----
        $entity = (object)[
            'company_id' => 10,
            'user_id'    => 55,
            'company'    => (object)[
                'company_key' => 'COMP123'
            ],
        ];

        // Mock documents() relationship
        $entity->documents = function () {
            return new class {
                public $saved = [];

                public function save($doc)
                {
                    // simulate DB save
                    $doc->id = rand(1, 9999);
                    $this->saved[] = $doc;
                }
            };
        };

        $entity->documents = $entity->documents();

        // ---- CREATE SOURCE DOCUMENT ----
        $source = Document::factory()->create([
            'id' => 111,
            'company_id' => 10,
            'user_id' => 77,
            'name' => 'invoice.pdf',
            'disk' => 'local',
            'size' => 123,
            'width' => 600,
            'height' => 800,
            'is_public' => true,
        ]);

        // Return fake file contents for getFile()
        Document::saving(function ($doc) {
            return true;
        });

        $source->getFile = function () {
            return 'FAKEPDFDATA';
        };

        // Replace getFile() method using Mockery
        $source = Mockery::mock($source)->makePartial();
        $source->shouldReceive('getFile')->andReturn('FAKEPDFDATA');

        // ---- DOCUMENT IDS ----
        $collection = new Collection([111]);

        // ---- MOCK MultiDB::setDb ----
        MultiDB::shouldReceive('setDb')
            ->once()
            ->with('db1');

        // ---- RUN JOB ----
        $job = new CopyDocs($collection, $entity, 'db1');
        $job->handle();

        // ---- ASSERT STORAGE PATH ----
        $savedDoc = $entity->documents->saved[0];  // the cloned document

        $this->assertNotNull($savedDoc->hash);
        $this->assertStringEndsWith('.pdf', $savedDoc->hash);

        $expectedPath = "COMP123/documents/" . $savedDoc->hash;

        Storage::disk('local')->assertExists($expectedPath);

        // ---- ASSERT NEW DOCUMENT PROPERTIES ----
        $this->assertEquals(55, $savedDoc->user_id);               // copied from entity
        $this->assertEquals(10, $savedDoc->company_id);
        $this->assertEquals('invoice.pdf', $savedDoc->name);
        $this->assertEquals('pdf', $savedDoc->type);
        $this->assertEquals('local', $savedDoc->disk);
        $this->assertEquals(123, $savedDoc->size);
        $this->assertEquals(600, $savedDoc->width);
        $this->assertEquals(800, $savedDoc->height);
        $this->assertTrue($savedDoc->is_public);
        $this->assertEquals($expectedPath, $savedDoc->url);
    }
}

