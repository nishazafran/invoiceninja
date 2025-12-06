<?php

namespace Tests\Events\Misc;

use Tests\TestCase;
use App\Events\Misc\InvitationWasViewed;
use App\Models\Company;
use App\Models\Invoice;

class InvitationWasViewedTest extends TestCase
{
    public function test_invitation_was_viewed_properties_are_set()
    {
        $entity = new Invoice();
        $entity->id = 100;

        $invitation = (object)['id' => 50];

        $company = new Company();
        $company->id = 1;

        $event_vars = [
            'ip' => '127.0.0.1',
            'user_agent' => 'PHPUnit'
        ];

        $event = new InvitationWasViewed($entity, $invitation, $company, $event_vars);

        $this->assertSame($entity, $event->entity);
        $this->assertSame($invitation, $event->invitation);
        $this->assertSame($company, $event->company);
        $this->assertSame($event_vars, $event->event_vars);
    }
}

