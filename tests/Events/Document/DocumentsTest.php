<?php

namespace Tests\Events\Document;

use Tests\TestCase;
use App\Models\Document;
use App\Models\Company;
use App\Events\Document\DocumentWasCreated;
use App\Events\Document\DocumentWasUpdated;
use App\Events\Document\DocumentWasDeleted;
use App\Events\Document\DocumentWasArchived;
use App\Events\Document\DocumentWasRestored;

class DocumentsTest extends TestCase
{
    private function makeDocument()
    {
        $doc = new Document();
        $doc->id = 1;
        return $doc;
    }

    private function makeCompany()
    {
        $company = new Company();
        $company->id = 99;
        return $company;
    }

    private function vars()
    {
        return ['x' => 10, 'y' => 20];
    }

    public function test_document_was_created()
    {
        $doc = $this->makeDocument();
        $company = $this->makeCompany();
        $vars = $this->vars();

        $event = new DocumentWasCreated($doc, $company, $vars);

        $this->assertSame($doc, $event->document);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }

    public function test_document_was_updated()
    {
        $doc = $this->makeDocument();
        $company = $this->makeCompany();
        $vars = $this->vars();

        $event = new DocumentWasUpdated($doc, $company, $vars);

        $this->assertSame($doc, $event->document);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }

    public function test_document_was_deleted()
    {
        $doc = $this->makeDocument();
        $company = $this->makeCompany();
        $vars = $this->vars();

        $event = new DocumentWasDeleted($doc, $company, $vars);

        $this->assertSame($doc, $event->document);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }

    public function test_document_was_archived()
    {
        $doc = $this->makeDocument();
        $company = $this->makeCompany();
        $vars = $this->vars();

        $event = new DocumentWasArchived($doc, $company, $vars);

        $this->assertSame($doc, $event->document);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
        $this->assertEquals([], $event->broadcastOn());
    }

    public function test_document_was_restored()
    {
        $doc = $this->makeDocument();
        $company = $this->makeCompany();
        $vars = $this->vars();

        $event = new DocumentWasRestored($doc, true, $company, $vars);

        $this->assertSame($doc, $event->document);
        $this->assertTrue($event->fromDeleted);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }
}

