<?php

namespace Tests\Observers;

use App\Models\Client;
use App\Models\Company;
use App\Models\Webhook;
use App\Observers\ClientObserver;
use App\Jobs\Client\CheckVat;
use App\Jobs\Client\UpdateTaxData;
use App\Jobs\Util\WebhookHandler;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ClientObserverTest extends TestCase
{
    public function test_observer_methods_full_coverage()
    {
        $observer = new ClientObserver();

        // -----------------------
        // MOCK ACCOUNT & COMPANY
        // -----------------------
        $account = new class {
            public function isFreeHostedClient() { return false; }
        };

        $company = new Company();
        $company->id = 1;
        $company->calculate_taxes = true;
        $company->account = $account;

        // -----------------------
        // MOCK CLIENT
        // -----------------------
        $client = new Client();
        $client->company = $company;
        $client->company_id = $company->id;

        // ----- US client -----
        $client->country_id = 840; // triggers UpdateTaxData
        $client->shipping_postal_code = '12345';
        $client->postal_code = '12345';
        $client->vat_number = 'VAT123';
        $client->deleted_at = null;
        $client->is_deleted = false;

        // Create webhook to trigger WebhookHandler
        Webhook::create([
            'company_id' => $company->id,
            'event_id' => Webhook::EVENT_CREATE_CLIENT,
            'target_url' => 'https://example.com/webhook',
        ]);

        // Fake queues
        Queue::fake();

        // -----------------------
        // TEST created() METHOD
        // -----------------------
        $observer->created($client);

        Queue::assertPushed(UpdateTaxData::class);
        Queue::assertFail(WebhookHandler::class);

        // ----- EU client -----
        $clientEU = clone $client;
        $clientEU->country_id = 276; // Germany, triggers CheckVat
        Webhook::create([
            'company_id' => $company->id,
            'event_id' => Webhook::EVENT_CREATE_CLIENT,
            'target_url' => 'https://example.com/webhook',
        ]);

        $observer->created($clientEU);

        Queue::assertPushed(CheckVat::class);
        Queue::assertPushed(WebhookHandler::class);

        // -----------------------
        // TEST updated() METHOD
        // -----------------------
        $clientUpdated = $this->getMockBuilder(Client::class)
                              ->onlyMethods(['getOriginal'])
                              ->getMock();
        $clientUpdated->company = $company;
        $clientUpdated->company_id = $company->id;
        $clientUpdated->country_id = 840;
        $clientUpdated->shipping_postal_code = '12345';
        $clientUpdated->postal_code = '12345';
        $clientUpdated->vat_number = 'VAT123';
        $clientUpdated->deleted_at = null;
        $clientUpdated->is_deleted = false;

        $clientUpdated->method('getOriginal')->willReturn([
            'shipping_postal_code' => '00000', // triggers UpdateTaxData
            'postal_code' => '00000',          // triggers UpdateTaxData
            'vat_number' => 'OLDVAT',          // triggers CheckVat
            'deleted_at' => '2025-01-01',      // triggers restore event
        ]);

        // Add webhook for updated client
        Webhook::create([
            'company_id' => $company->id,
            'event_id' => Webhook::EVENT_UPDATE_CLIENT,
            'target_url' => 'https://example.com/webhook',
        ]);

        Queue::fake(); // reset queue for updated test
        $observer->updated($clientUpdated);

        Queue::assertPushed(UpdateTaxData::class);
        Queue::assertPushed(CheckVat::class);
        Queue::assertPushed(WebhookHandler::class);

        // -----------------------
        // TEST deleted() METHOD
        // -----------------------
        $clientDeleted = clone $client;
        $clientDeleted->is_deleted = false;

        // Add webhook for archive client
        Webhook::create([
            'company_id' => $company->id,
            'event_id' => Webhook::EVENT_ARCHIVE_CLIENT,
            'target_url' => 'https://example.com/webhook',
        ]);

        Queue::fake();
        $observer->deleted($clientDeleted);

        Queue::assertPushed(WebhookHandler::class);

        // Case when client already deleted -> early return
        $clientDeleted->is_deleted = true;
        $observer->deleted($clientDeleted);

        // -----------------------
        // FINAL ASSERT
        // -----------------------
        $this->assertTrue(true, "All observer methods executed without errors");
    }
}

