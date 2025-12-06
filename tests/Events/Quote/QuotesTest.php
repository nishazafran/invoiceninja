<?php

namespace Tests\Events\Quote;

use Tests\TestCase;
use App\Models\Quote;
use App\Models\QuoteInvitation;
use App\Models\Company;

use App\Events\Quote\QuoteWasViewed;
use App\Events\Quote\QuoteWasMarkedApproved;
use App\Events\Quote\QuoteWasEmailedAndFailed;
use App\Events\Quote\QuoteWasEmailed;
use App\Events\Quote\QuoteReminderWasEmailed;

class QuotesTest extends TestCase
{
    private function fakeQuote()
    {
        return new Quote();
    }

    private function fakeInvitation()
    {
        return new QuoteInvitation();
    }

    private function fakeCompany()
    {
        return new Company();
    }

    private function fakeEventVars()
    {
        return ['key' => 'value'];
    }

    private function fakeErrors()
    {
        return ['error1', 'error2'];
    }

    private function fakeTemplate()
    {
        return 'template_name';
    }

    // ------------------- QuoteWasViewed.php -------------------
    public function test_quote_was_viewed()
    {
        $invitation = $this->fakeInvitation();
        $company = $this->fakeCompany();
        $vars = $this->fakeEventVars();

        $event = new QuoteWasViewed($invitation, $company, $vars);

        $this->assertSame($invitation, $event->invitation);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }

    // ------------------- QuoteWasMarkedApproved.php -------------------
    public function test_quote_was_marked_approved()
    {
        $quote = $this->fakeQuote();
        $company = $this->fakeCompany();
        $vars = $this->fakeEventVars();

        $event = new QuoteWasMarkedApproved($quote, $company, $vars);

        $this->assertSame($quote, $event->quote);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }

    // ------------------- QuoteWasEmailedAndFailed.php -------------------
    public function test_quote_was_emailed_and_failed()
    {
        $quote = $this->fakeQuote();
        $company = $this->fakeCompany();
        $errors = $this->fakeErrors();
        $vars = $this->fakeEventVars();

        $event = new QuoteWasEmailedAndFailed($quote, $errors, $company, $vars);

        $this->assertSame($quote, $event->quote);
        $this->assertSame($errors, $event->errors);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }

    // ------------------- QuoteWasEmailed.php -------------------
    public function test_quote_was_emailed()
    {
        $invitation = $this->fakeInvitation();
        $company = $this->fakeCompany();
        $vars = $this->fakeEventVars();
        $template = $this->fakeTemplate();

        $event = new QuoteWasEmailed($invitation, $company, $vars, $template);

        $this->assertSame($invitation, $event->invitation);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
        $this->assertSame($template, $event->template);
    }

    // ------------------- QuoteReminderWasEmailed.php -------------------
    public function test_quote_reminder_was_emailed()
    {
        $invitation = $this->fakeInvitation();
        $company = $this->fakeCompany();
        $vars = $this->fakeEventVars();
        $template = $this->fakeTemplate();

        $event = new QuoteReminderWasEmailed($invitation, $company, $vars, $template);

        $this->assertSame($invitation, $event->invitation);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
        $this->assertSame($template, $event->template);
    }
}

