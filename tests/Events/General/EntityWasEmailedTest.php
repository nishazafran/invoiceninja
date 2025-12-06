<?php

namespace Tests\Events\General;

use Tests\TestCase;
use App\Models\Company;
use App\Events\General\EntityWasEmailed;

class EntityWasEmailedTest extends TestCase
{
    public function test_entity_was_emailed_properties_are_set()
    {
        $invitation = (object) ['id' => 123];

        $company = new Company();
        $company->id = 10;

        $event_vars = ['foo' => 'bar'];
        $template = 'invoice';

        $event = new EntityWasEmailed($invitation, $company, $event_vars, $template);

        $this->assertSame($invitation, $event->invitation);
        $this->assertSame($company, $event->company);
        $this->assertSame($event_vars, $event->event_vars);
        $this->assertSame($template, $event->template);
    }
}

