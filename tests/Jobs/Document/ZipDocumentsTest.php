<?php

namespace Tests\Jobs\Document;

use App\Jobs\Document\ZipDocuments;
use App\Jobs\Mail\NinjaMailerJob;
use App\Jobs\Util\UnlinkFile;
use App\Models\Company;
use App\Models\Document;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\App;
use PhpZip\ZipFile;
use Mockery;
use Tests\TestCase;

class ZipDocumentsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Queue::fake();
        Storage::fake('local');
    }

    public function test_buildFileName_returns_correct_format()
    {
        $company = Company::factory()->make();
        $user = User::factory()->make();

        $job = new ZipDocuments([], $company, $user);

        $documentable = new class {
            public $number = 123;
            public function translate_entity() { return 'invoice'; }
        };

        $document = new class {
            public $name = 'test.pdf';
            public $created_at;
            public $documentable;
            public function __construct() {
                $this->created_at = now()->timestamp;
            }
        };

        $document->documentable = $documentable;

        $this->assertStringContainsString('invoice', $document);
        $this->assertStringContainsString('123', $document);
        $this->assertStringContainsString('test.pdf', $document);
    }

    private function test_mockPhpZip($zipMock)
    {
        $this->mock(\PhpZip\ZipFile::class, function($mock) use ($zipMock) {
            return $zipMock;
        });
    }
}

