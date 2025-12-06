<?php

namespace Tests\Utils\ClientPortal\CustomMessage;

use App\Models\Client;
use App\Models\ClientContact;
use App\Models\Company;
use App\Models\GroupSetting;
use App\Utils\ClientPortal\CustomMessage\CustomMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomMessageTest extends TestCase
{
    public function test_replaces_company_and_client_placeholders()
    {
        $company = new Company();
        $company->id = 1;
        $company->settings = ['name' => 'TestCo', 'website' => 'https://test.com'];

        $client = new Client();
        $client->id = 10;
        $client->name = "John Doe";

        $msg = 'Hello $client.name from $company.name ($company.id)';

        $service = (new CustomMessage())
            ->company($company)
            ->client($client);

        $output = $service->message($msg);

        $this->assertEquals("Hello John Doe from TestCo (1)", $output);
    }

    public function test_replaces_contact_placeholders()
    {
        $contact = new ClientContact();
        $contact->first_name = "Alice";
        $contact->last_name = "Smith";
        $contact->email = "alice@example.com";
        $contact->avatar = null; // Added to prevent Undefined property

        $service = (new CustomMessage())->contact($contact);

        $msg = 'Contact: $contact.first_name $contact.last_name <$contact.email>';

        $output = $service->message($msg);

        $this->assertEquals("Contact: Alice Smith <alice@example.com>", $output);
    }

    public function test_replaces_group_placeholder()
    {
        $group = new GroupSetting();
        $group->id = 555;

        $service = (new CustomMessage())->group($group);

        $msg = 'Group: $group.id';

        $output = $service->message($msg);

        $this->assertEquals("Group: 555", $output);
    }

    public function test_replaces_entity_placeholders()
    {
        $entity = new class {
            public $hashed_id = "abc123"; // Added to match compose()
            public $number = "INV-1001";
            public $amount = 500;
            public $discount = null; // Added to prevent Undefined property
            public $date = null;
            public $due_date = null;
            public $last_sent_date = null;
            public $public_notes = null;
            public $terms = null;
            public $balance = null;
            public $created_at = null;
            public $status_id = null;
            public $project = null;
            
            public function badgeForStatus($status_id)
    	   {
        	return "Paid"; // or any dummy value
    	   }
        };

        $service = (new CustomMessage())->entity($entity);

        $msg = 'Invoice $entity.number ($entity.id) amount $entity.amount';

        $output = $service->message($msg);

        $this->assertEquals("Invoice INV-1001 (abc123) amount 500", $output);
    }

    public function test_returns_original_message_when_no_values_are_set()
    {
        $msg = 'Hello $client.name from $company.name';

        $service = new CustomMessage();

        $output = $service->message($msg);

        $this->assertEquals("Hello  from ", $output);
    }

    public function test_handles_mixed_fields_entity_company_client()
    {
        $company = new Company();
        $company->id = 77;
        $company->settings = ['name' => 'BigCorp'];

        $client = new Client();
        $client->name = "Buyer Inc";

        $entity = new class {
            public $hashed_id = "xyz789"; // Added to prevent error
            public $number = "SO-22";
            public $discount = null;
            public $date = null;
            public $due_date = null;
            public $last_sent_date = null;
            public $public_notes = null;
            public $terms = null;
            public $amount = null;
            public $balance = null;
            public $created_at = null;
            public $status_id = null;
            public $project = null;
            
            public function badgeForStatus($status_id)
    	   {
        	return "Paid"; // or any dummy value
    	   }
        };

        $msg = '$company.name sells to $client.name on $entity.number';

        $service = (new CustomMessage())
            ->company($company)
            ->client($client)
            ->entity($entity);

        $output = $service->message($msg);

        $this->assertEquals("BigCorp sells to Buyer Inc on SO-22", $output);
    }
    
    public function test_sets_invitation()
{
    $invitation = new class {
        public $id = 123;
        public $user = null;
        public $sent_date = null;
        public $viewed_date = null;
        public $opened_date = null;
    };

    $service = (new CustomMessage())->invitation($invitation);

    $msg = 'Invitation ID: $invitation.id';

    $output = $service->message($msg);

    $this->assertEquals('Invitation ID: 123', $output);
}

}

