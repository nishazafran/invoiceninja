<?php

namespace Tests\Events\Statement;

use Tests\TestCase;
use App\Models\Client;
use App\Models\Company;
use App\Events\Statement\StatementWasEmailed;

class StatementWasEmailedTest extends TestCase
{
    private function fakeClientAndCompany()
    {
        $company = new Company();
        $company->id = 1;
        $company->name = 'Test Company';

        $client = new Client();
        $client->id = 1;
        $client->name = 'Test Client';

        return [$client, $company];
    }

    public function test_statement_was_emailed_event()
    {
        [$client, $company] = $this->fakeClientAndCompany();
        $endDate = '2025-12-31';
        $eventVars = ['foo' => 'bar'];

        $event = new StatementWasEmailed($client, $company, $endDate, $eventVars);

        // Checking properties
        $this->assertSame($client, $event->client);
        $this->assertSame($company, $event->company);
        $this->assertSame($endDate, $event->end_date);
        $this->assertSame($eventVars, $event->event_vars);

        // Checking broadcasting channels (should be empty)
        $channels = $event->broadcastOn();
        $this->assertIsArray($channels);
        $this->assertCount(0, $channels);
    }
}

