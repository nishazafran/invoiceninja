<?php

namespace Tests\Events\Design;

use Tests\TestCase;
use App\Models\Design;
use App\Models\Company;
use App\Events\Design\DesignWasCreated;
use App\Events\Design\DesignWasUpdated;
use App\Events\Design\DesignWasArchived;
use App\Events\Design\DesignWasDeleted;
use App\Events\Design\DesignWasRestored;

class DesignsTest extends TestCase
{
    private function vars()
    {
        return ['a' => 1, 'b' => 2];
    }

    private function makeDesign()
    {
        $design = new Design();
        $design->id = 1;
        return $design;
    }

    private function makeCompany()
    {
        $company = new Company();
        $company->id = 10;
        return $company;
    }

    public function test_design_was_created()
    {
        $design = $this->makeDesign();
        $company = $this->makeCompany();
        $vars = $this->vars();

        $event = new DesignWasCreated($design, $company, $vars);

        $this->assertSame($design, $event->design);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
        $this->assertEquals([], $event->broadcastOn());
    }

    public function test_design_was_updated()
    {
        $design = $this->makeDesign();
        $company = $this->makeCompany();
        $vars = $this->vars();

        $event = new DesignWasUpdated($design, $company, $vars);

        $this->assertSame($design, $event->design);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
        $this->assertEquals([], $event->broadcastOn());
    }

    public function test_design_was_archived()
    {
        $design = $this->makeDesign();
        $company = $this->makeCompany();
        $vars = $this->vars();

        $event = new DesignWasArchived($design, $company, $vars);

        $this->assertSame($design, $event->design);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
        $this->assertEquals([], $event->broadcastOn());
    }

    public function test_design_was_deleted()
    {
        $design = $this->makeDesign();
        $company = $this->makeCompany();
        $vars = $this->vars();

        $event = new DesignWasDeleted($design, $company, $vars);

        $this->assertSame($design, $event->design);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
        $this->assertEquals([], $event->broadcastOn());
    }

    public function test_design_was_restored()
    {
        $design = $this->makeDesign();
        $company = $this->makeCompany();
        $vars = $this->vars();

        $event = new DesignWasRestored($design, true, $company, $vars);

        $this->assertSame($design, $event->design);
        $this->assertTrue($event->fromDeleted);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
        $this->assertEquals([], $event->broadcastOn());
    }
}

