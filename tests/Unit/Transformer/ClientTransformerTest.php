<?php

namespace Tests\Unit\Transformers;

use App\Models\Client;
use App\Models\Activity;
use App\Models\Document;
use App\Models\Location;
use App\Models\SystemLog;
use App\Models\GroupSetting;
use App\Models\ClientContact;
use App\Models\ClientGatewayToken;
use App\Models\CompanyLedger;
use App\Transformers\ClientTransformer;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use Tests\TestCase;

class ClientTransformerTest extends TestCase
{
    private ClientTransformer $transformer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transformer = new ClientTransformer();
    }

    /** @test */
    public function test_transform_returns_correct_array()
    {
        $client = new Client();
        $client->id = 1;
        $client->user_id = 2;
        $client->assigned_user_id = 3;
        $client->name = 'Test Client';
        $client->website = 'https://example.com';
        $client->private_notes = 'Notes';
        $client->balance = 100.0;
        $client->group_settings_id = 5;
        $client->paid_to_date = 50.0;
        $client->payment_balance = 10.0;
        $client->credit_balance = 5.0;
        $client->last_login = 1699999999;
        $client->size_id = 1;
        $client->public_notes = 'Public notes';
        $client->client_hash = 'hash';
        $client->address1 = '123 Street';
        $client->address2 = 'Apt 4';
        $client->phone = '1234567890';
        $client->city = 'City';
        $client->state = 'State';
        $client->postal_code = '12345';
        $client->country_id = 1;
        $client->industry_id = 2;
        $client->custom_value1 = 'CV1';
        $client->custom_value2 = 'CV2';
        $client->custom_value3 = 'CV3';
        $client->custom_value4 = 'CV4';
        $client->shipping_address1 = 'Ship Addr1';
        $client->shipping_address2 = 'Ship Addr2';
        $client->shipping_city = 'Ship City';
        $client->shipping_state = 'Ship State';
        $client->shipping_postal_code = 'Ship Postal';
        $client->shipping_country_id = 3;
        $client->settings = new \stdClass();
        $client->is_deleted = false;
        $client->vat_number = 'VAT123';
        $client->id_number = 'ID123';
        $client->updated_at = 1700000000;
        $client->deleted_at = null;
        $client->created_at = 1699999999;
        $client->has_valid_vat_number = true;
        $client->is_tax_exempt = false;
        $client->routing_id = 'R123';
        $client->tax_data = new \stdClass();
        $client->classification = 'ClassA';
        $client->e_invoice = new \stdClass();

        $result = $this->transformer->transform($client);

        $this->assertEquals($this->transformer->encodePrimaryKey(1), $result['id']);
        $this->assertEquals($this->transformer->encodePrimaryKey(2), $result['user_id']);
        $this->assertEquals($this->transformer->encodePrimaryKey(3), $result['assigned_user_id']);
        $this->assertEquals('Test Client', $result['name']);
        $this->assertEquals('https://example.com', $result['website']);
        $this->assertEquals(100.0, $result['balance']);
        $this->assertEquals('4openRe7Az', $result['group_settings_id']);
        $this->assertEquals(1699999999, $result['last_login']);
        $this->assertFalse($result['is_deleted']);
        $this->assertEquals(1700000000, $result['updated_at']);
        $this->assertEquals(1699999999, $result['created_at']);
    }

    /** @test */
    public function test_include_methods_return_collection_or_item()
    {
        $client = new Client();
        $client->activities = [new Activity()];
        $client->documents = [new Document()];
        $client->contacts = [new ClientContact()];
        $client->locations = [new Location()];
        $client->gateway_tokens = [new ClientGatewayToken()];
        $client->ledger = [new CompanyLedger()];
        $client->system_logs = [new SystemLog()];
        $client->group_settings = new GroupSetting();

        $this->assertInstanceOf(Collection::class, $this->transformer->includeActivities($client));
        $this->assertInstanceOf(Collection::class, $this->transformer->includeDocuments($client));
        $this->assertInstanceOf(Collection::class, $this->transformer->includeContacts($client));
        $this->assertInstanceOf(Collection::class, $this->transformer->includeLocations($client));
        $this->assertInstanceOf(Collection::class, $this->transformer->includeGatewayTokens($client));
        $this->assertInstanceOf(Collection::class, $this->transformer->includeLedger($client));
        $this->assertInstanceOf(Collection::class, $this->transformer->includeSystemLogs($client));
        $this->assertInstanceOf(Item::class, $this->transformer->includeGroupSettings($client));
    }

    /** @test */
    public function test_include_group_settings_returns_null_when_missing()
    {
        $client = new Client();
        $client->group_settings = null;

        $this->assertNull($this->transformer->includeGroupSettings($client));
    }
}

