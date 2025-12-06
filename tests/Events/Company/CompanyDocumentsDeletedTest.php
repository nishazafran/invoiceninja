<?php

namespace Tests\Events\Company;

use App\Events\Company\CompanyDocumentsDeleted;
use App\Models\Company;
use PHPUnit\Framework\TestCase;

class CompanyDocumentsDeletedTest extends TestCase
{

    public function test_can_be_instantiated_and_broadcast_on()
    {
        $company = new Company(['name' => 'Acme Inc.']);

        $event = new CompanyDocumentsDeleted($company);

        // Check the property is set
        $this->assertEquals($company, $event->company);

        // Check broadcastOn method
        $this->assertEquals([], $event->broadcastOn());
    }
}

