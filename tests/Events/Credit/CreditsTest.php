<?php

namespace Tests\Events\Credit;

use App\Events\Credit\CreditWasEmailed;
use App\Events\Credit\CreditWasEmailedAndFailed;
use App\Events\Credit\CreditWasViewed;
use App\Models\Company;
use App\Models\Credit;
use App\Models\CreditInvitation;
use Tests\TestCase;

class CreditsTest extends TestCase
{
    public function test_instantiate_credit_was_emailed()
    {
        $invitation = CreditInvitation::factory()->make();
        $company = Company::factory()->make();
        $eventVars = ['key' => 'value'];
        $template = 'default';

        $event = new CreditWasEmailed($invitation, $company, $eventVars, $template);

        $this->assertEquals($invitation, $event->invitation);
        $this->assertEquals($company, $event->company);
        $this->assertEquals($eventVars, $event->event_vars);
        $this->assertEquals($template, $event->template);
    }

    public function test_instantiate_credit_was_emailed_and_failed()
    {
        $credit = Credit::factory()->make();
        $company = Company::factory()->make();
        $errors = ['error' => 'Failed to send'];
        $eventVars = ['key' => 'value'];

        $event = new CreditWasEmailedAndFailed($credit, $company, $errors, $eventVars);

        $this->assertEquals($credit, $event->credit);
        $this->assertEquals($company, $event->company);
        $this->assertEquals($errors, $event->errors);
        $this->assertEquals($eventVars, $event->event_vars);
    }

    public function test_instantiate_credit_was_viewed()
    {
        $invitation = CreditInvitation::factory()->make();
        $company = Company::factory()->make();
        $eventVars = ['key' => 'value'];

        $event = new CreditWasViewed($invitation, $company, $eventVars);

        $this->assertEquals($invitation, $event->invitation);
        $this->assertEquals($company, $event->company);
        $this->assertEquals($eventVars, $event->event_vars);
    }
}

