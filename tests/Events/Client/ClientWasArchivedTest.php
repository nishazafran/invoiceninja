<?php

namespace Tests\Events\Client;

use App\Events\Client\ClientWasArchived;
use App\Models\Client;
use App\Models\Company;
use App\Transformers\ArraySerializer;
use Illuminate\Broadcasting\PrivateChannel;
use League\Fractal\Manager;
use Tests\TestCase;

class ClientWasArchivedTest extends TestCase
{
    public function makeMinimalClient()
    {
        // Allow mass assignment
        Client::unguard();

        // Stub for presenter()
        $presenter = new class {
            public function name()
            {
                return 'Presented Name';
            }
        };

        $client = new Client([
            'name' => 'Test Client',
            'id' => 1,
            'user_id' => 2,
            'assigned_user_id' => 3,
            'website' => '',
            'private_notes' => '',
            'balance' => 0,
            'group_settings_id' => '',
            'paid_to_date' => 0,
            'payment_balance' => 0,
            'credit_balance' => 0,
            'last_login' => 0,
            'size_id' => 1,
            'public_notes' => '',
            'client_hash' => 'xyz',
            'address1' => '',
            'address2' => '',
            'phone' => '',
            'city' => '',
            'state' => '',
            'postal_code' => '',
            'country_id' => '',
            'industry_id' => '',
            'custom_value1' => '',
            'custom_value2' => '',
            'custom_value3' => '',
            'custom_value4' => '',
            'shipping_address1' => '',
            'shipping_address2' => '',
            'shipping_city' => '',
            'shipping_state' => '',
            'shipping_postal_code' => '',
            'shipping_country_id' => '',
            'settings' => new \stdClass(),
            'is_deleted' => false,
            'vat_number' => '',
            'id_number' => '',
            'updated_at' => 0,
            'deleted_at' => 0,
            'created_at' => 0,
            'number' => 'C001',
            'has_valid_vat_number' => false,
            'is_tax_exempt' => false,
            'routing_id' => '',
            'tax_data' => new \stdClass(),
            'classification' => '',
            'e_invoice' => new \stdClass(),
        ]);

        // Required by ClientTransformer
        $client->setRelation('contacts', collect());
        $client->setRelation('documents', collect());
        $client->setRelation('locations', collect());
        $client->setRelation('gateway_tokens', collect());

        // Required for optional includes
        $client->setRelation('activities', collect());
        $client->setRelation('ledger', collect());
        $client->setRelation('system_logs', collect());
        $client->setRelation('group_settings', null);

        $client->presenter = $presenter;

        // Stub getEntityType()
        $client->getEntityType = fn () => 'client';

        return $client;
    }

    public function makeCompany()
    {
        Company::unguard();
        return new Company(['company_key' => 'abc123']);
    }

    public function test_constructor_assigns_properties()
    {
        $client = $this->makeMinimalClient();
        $company = $this->makeCompany();

        $event_vars = ['a' => 1];
        $event = new ClientWasArchived($client, $company, $event_vars);

        $this->assertSame($client, $event->client);
        $this->assertSame($company, $event->company);
        $this->assertSame($event_vars, $event->event_vars);
    }

    public function test_broadcastOn_returns_correct_channel()
    {
        $event = new ClientWasArchived(
            $this->makeMinimalClient(),
            $this->makeCompany(),
            []
        );

        $channels = $event->broadcastOn();

        $this->assertIsArray($channels);
        $this->assertInstanceOf(PrivateChannel::class, $channels[0]);
        $this->assertEquals('private-company-abc123', (string) $channels[0]);
    }

    public function test_broadcastWith_transforms_client_correctly()
    {
        $client = $this->makeMinimalClient();
        $company = $this->makeCompany();

        $event = new ClientWasArchived($client, $company, []);

        $data = $event->broadcastWith();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('name', $data);   // part of transformer
        $this->assertEquals('Test Client', $data['name']);

        // Make sure includes were processed
        $this->assertArrayHasKey('contacts', $data);
        $this->assertArrayHasKey('documents', $data);
        $this->assertArrayHasKey('locations', $data);
        $this->assertArrayHasKey('gateway_tokens', $data);

        // ensure presenter was called
        $this->assertEquals('Test Client', $data['display_name']);
    }
}

