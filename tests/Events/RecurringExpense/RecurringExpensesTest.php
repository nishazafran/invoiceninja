<?php

namespace Tests\Events\RecurringExpense;

use Tests\TestCase;
use App\Models\RecurringExpense;
use App\Models\Company;

use App\Events\RecurringExpense\RecurringExpenseWasArchived;
use App\Events\RecurringExpense\RecurringExpenseWasCreated;
use App\Events\RecurringExpense\RecurringExpenseWasDeleted;
use App\Events\RecurringExpense\RecurringExpenseWasUpdated;
use App\Events\RecurringExpense\RecurringExpenseWasRestored;

class RecurringExpensesTest extends TestCase
{
    private function fakeRecurringExpense()
    {
        return new RecurringExpense();
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

    // ------------------- RecurringExpenseWasArchived.php -------------------
    public function test_recurring_expense_was_archived()
    {
        $expense = $this->fakeRecurringExpense();
        $company = $this->fakeCompany();
        $vars = $this->fakeEventVars();

        $event = new RecurringExpenseWasArchived($expense, $company, $vars);

        $this->assertSame($expense, $event->recurring_expense);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }

    // ------------------- RecurringExpenseWasCreated.php -------------------
    public function test_recurring_expense_was_created()
    {
        $expense = $this->fakeRecurringExpense();
        $company = $this->fakeCompany();
        $vars = $this->fakeEventVars();

        $event = new RecurringExpenseWasCreated($expense, $company, $vars);

        $this->assertSame($expense, $event->recurring_expense);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }

    // ------------------- RecurringExpenseWasDeleted.php -------------------
    public function test_recurring_expense_was_deleted()
    {
        $expense = $this->fakeRecurringExpense();
        $company = $this->fakeCompany();
        $vars = $this->fakeEventVars();

        $event = new RecurringExpenseWasDeleted($expense, $company, $vars);

        $this->assertSame($expense, $event->recurring_expense);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }

    // ------------------- RecurringExpenseWasUpdated.php -------------------
    public function test_recurring_expense_was_updated()
    {
        $expense = $this->fakeRecurringExpense();
        $company = $this->fakeCompany();
        $vars = $this->fakeEventVars();

        $event = new RecurringExpenseWasUpdated($expense, $company, $vars);

        $this->assertSame($expense, $event->recurring_expense);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }

    // ------------------- RecurringExpenseWasRestored.php -------------------
    public function test_recurring_expense_was_restored()
    {
        $expense = $this->fakeRecurringExpense();
        $company = $this->fakeCompany();
        $vars = $this->fakeEventVars();
        $fromDeleted = $this->fakeFromDeleted();

        $event = new RecurringExpenseWasRestored($expense, $fromDeleted, $company, $vars);

        $this->assertSame($expense, $event->recurring_expense);
        $this->assertTrue($event->fromDeleted);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }
}

