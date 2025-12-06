<?php

namespace Tests\Events\RecurringQuote;

use Tests\TestCase;
use App\Models\RecurringQuote;
use App\Models\Company;

use App\Events\RecurringQuote\RecurringQuoteWasArchived;
use App\Events\RecurringQuote\RecurringQuoteWasCreated;
use App\Events\RecurringQuote\RecurringQuoteWasDeleted;
use App\Events\RecurringQuote\RecurringQuoteWasRestored;
use App\Events\RecurringQuote\RecurringQuoteWasUpdated;

class RecurringQuotesTest extends TestCase
{
    private function fakeRecurringQuote()
    {
        return new RecurringQuote();
    }

    private function fakeCompany()
    {
        return new Company();
    }

    private function fakeEventVars()
    {
        return ['key' => 'value'];
    }

    private function fakeFromDeleted()
    {
        return true;
    }

    // ------------------- RecurringQuoteWasArchived.php -------------------
    public function test_recurring_quote_was_archived()
    {
        $quote = $this->fakeRecurringQuote();
        $company = $this->fakeCompany();
        $vars = $this->fakeEventVars();

        $event = new RecurringQuoteWasArchived($quote, $company, $vars);

        $this->assertSame($quote, $event->recurring_quote);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }

    // ------------------- RecurringQuoteWasCreated.php -------------------
    public function test_recurring_quote_was_created()
    {
        $quote = $this->fakeRecurringQuote();
        $company = $this->fakeCompany();
        $vars = $this->fakeEventVars();

        $event = new RecurringQuoteWasCreated($quote, $company, $vars);

        $this->assertSame($quote, $event->recurring_quote);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }

    // ------------------- RecurringQuoteWasDeleted.php -------------------
    public function test_recurring_quote_was_deleted()
    {
        $quote = $this->fakeRecurringQuote();
        $company = $this->fakeCompany();
        $vars = $this->fakeEventVars();

        $event = new RecurringQuoteWasDeleted($quote, $company, $vars);

        $this->assertSame($quote, $event->recurring_quote);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }

    // ------------------- RecurringQuoteWasRestored.php -------------------
    public function test_recurring_quote_was_restored()
    {
        $quote = $this->fakeRecurringQuote();
        $company = $this->fakeCompany();
        $vars = $this->fakeEventVars();
        $fromDeleted = $this->fakeFromDeleted();

        $event = new RecurringQuoteWasRestored($quote, $fromDeleted, $company, $vars);

        $this->assertSame($quote, $event->recurring_quote);
        $this->assertTrue($event->fromDeleted);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }

    // ------------------- RecurringQuoteWasUpdated.php -------------------
    public function test_recurring_quote_was_updated()
    {
        $quote = $this->fakeRecurringQuote();
        $company = $this->fakeCompany();
        $vars = $this->fakeEventVars();

        $event = new RecurringQuoteWasUpdated($quote, $company, $vars);

        $this->assertSame($quote, $event->recurring_quote);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }
}

