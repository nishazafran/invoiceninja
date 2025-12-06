<?php

namespace Tests\Utils;

use App\Models\Client;
use App\Models\Company;
use App\Models\ClientContact;
use App\Models\Invoice;
use App\Models\InvoiceInvitation;
use App\Models\Account;
use App\Utils\HtmlEngine;
use App\Utils\Helpers;
use Tests\TestCase;

class HtmlEngineTest extends TestCase
{
    private $invitation;
    private $invoice;
    private $client;
    private $contact;
    private $company;
    private $account;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock Account
        $this->account = $this->createMock(Account::class);
        $this->account->method('hasFeature')->willReturn(true);

        // Mock Company
        $this->company = $this->createMock(Company::class);
        $this->company->account = $this->account;

        // Mock Client
        $this->client = $this->createMock(ClientContact::class);
        $this->client->method('getMergedSettings')->willReturn((object)[
            'email_style' => 'plain',
            'primary_color' => '#ff0000',
            'embed_documents' => true,
        ]);

        // Mock Contact
        $this->contact = $this->createMock(ClientContact::class);
        $this->contact->client = $this->client;

        // Mock Invoice
        $this->invoice = $this->createMock(Invoice::class);
        $this->invoice->method('calc')->willReturn(['amount' => 100]);
        $this->invoice->method('documents')->willReturn(collect([]));
        $this->invoice->client = $this->client;

        // Mock Invitation
        $this->invitation = $this->createMock(InvoiceInvitation::class);
        $this->invitation->company = $this->company;
        $this->invitation->contact = $this->contact;
        $this->invitation->invoice = $this->invoice;
    }

    // ---------------- Existing tests ----------------
    public function test_makeValuesNoPrefix_returns_transformed_keys_and_values()
    {
        $engine = new HtmlEngine($this->invitation);

        // Mock buildEntityDataArray to return predictable data
        $reflection = new \ReflectionClass($engine);
        $method = $reflection->getMethod('buildEntityDataArray');
        $method->setAccessible(true);
        $method->invoke($engine);

        // Override buildEntityDataArray for test
        $engine = $this->getMockBuilder(HtmlEngine::class)
            ->setConstructorArgs([$this->invitation])
            ->onlyMethods(['buildEntityDataArray'])
            ->getMock();

        $engine->method('buildEntityDataArray')->willReturn([
            'invoice.amount' => ['value' => 100],
            '$invoice.total' => ['value' => 200],
        ]);

        $result = $engine->makeValuesNoPrefix();

        $this->assertArrayHasKey('invoice_amount', $result);
        $this->assertArrayHasKey('_invoice_total', $result);
        $this->assertEquals(100, $result['invoice_amount']);
        $this->assertEquals(200, $result['_invoice_total']);
    }

    public function test_generateEntityImagesMarkup_returns_empty_if_feature_disabled()
    {
        $engine = new HtmlEngine($this->invitation);

        $this->client->method('getSetting')->with('embed_documents')->willReturn(false);

        $reflection = new \ReflectionClass($engine);
        $method = $reflection->getMethod('generateEntityImagesMarkup');
        $method->setAccessible(true);

        $result = $method->invoke($engine);
        $this->assertEquals('', $result);
    }

    public function test_buildViewButton_returns_plain_style()
    {
        $engine = new HtmlEngine($this->invitation);
        $engine->settings->email_style = 'plain';

        $reflection = new \ReflectionClass($engine);
        $method = $reflection->getMethod('buildViewButton');
        $method->setAccessible(true);

        $link = 'https://example.com';
        $text = 'Click Here';

        $html = $method->invoke($engine, $link, $text);

        $this->assertStringContainsString('<a href="https://example.com"', $html);
        $this->assertStringContainsString('Click Here', $html);
    }

    public function test_buildViewButton_returns_html_style()
    {
        $engine = new HtmlEngine($this->invitation);
        $engine->settings->email_style = 'html';
        $engine->settings->primary_color = '#123456';

        $reflection = new \ReflectionClass($engine);
        $method = $reflection->getMethod('buildViewButton');
        $method->setAccessible(true);

        $link = 'https://example.com';
        $text = 'Click Here';

        $html = $method->invoke($engine, $link, $text);

        $this->assertStringContainsString('background-color: #123456', $html);
        $this->assertStringContainsString('Click Here', $html);
        $this->assertStringContainsString('<a href="https://example.com"', $html);
    }

    // ---------------- New tests for full coverage ----------------

    public function test_makeValuesNoPrefix_handles_keys_with_dollar_and_dot()
    {
        $engine = $this->getMockBuilder(HtmlEngine::class)
            ->setConstructorArgs([$this->invitation])
            ->onlyMethods(['buildEntityDataArray'])
            ->getMock();

        $engine->method('buildEntityDataArray')->willReturn([
            'invoice.amount' => ['value' => 100],
            '$client.name' => ['value' => 'John Doe'],
        ]);

        $result = $engine->makeValuesNoPrefix();

        $this->assertArrayHasKey('invoice_amount', $result);
        $this->assertArrayHasKey('_client_name', $result);
        $this->assertEquals(100, $result['invoice_amount']);
        $this->assertEquals('John Doe', $result['_client_name']);
    }

    public function test_makeValuesNoPrefix_returns_empty_array_when_no_data()
    {
        $engine = $this->getMockBuilder(HtmlEngine::class)
            ->setConstructorArgs([$this->invitation])
            ->onlyMethods(['buildEntityDataArray'])
            ->getMock();

        $engine->method('buildEntityDataArray')->willReturn([]);

        $result = $engine->makeValuesNoPrefix();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_generateEntityImagesMarkup_returns_markup_when_documents_exist()
    {
        $engine = $this->getMockBuilder(HtmlEngine::class)
            ->setConstructorArgs([$this->invitation])
            ->onlyMethods(['getDocuments'])
            ->getMock();

        $engine->method('getDocuments')->willReturn(['doc1.pdf', 'doc2.pdf']);
        $this->client->method('getSetting')->with('embed_documents')->willReturn(true);

        $reflection = new \ReflectionClass($engine);
        $method = $reflection->getMethod('generateEntityImagesMarkup');
        $method->setAccessible(true);

        $result = $method->invoke($engine);

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function test_buildViewButton_generates_both_plain_and_html_variants()
    {
        $engine = new HtmlEngine($this->invitation);

        // Plain
        $engine->settings->email_style = 'plain';
        $reflection = new \ReflectionClass($engine);
        $method = $reflection->getMethod('buildViewButton');
        $method->setAccessible(true);

        $plainHtml = $method->invoke($engine, 'https://plain.com', 'Plain Link');
        $this->assertStringContainsString('Plain Link', $plainHtml);

        // HTML
        $engine->settings->email_style = 'html';
        $engine->settings->primary_color = '#abcdef';
        $htmlHtml = $method->invoke($engine, 'https://html.com', 'HTML Link');

        $this->assertStringContainsString('HTML Link', $htmlHtml);
        $this->assertStringContainsString('background-color: #abcdef', $htmlHtml);
    }

    public function test_getPaymentMeta_returns_expected_array()
    {
        $engine = new HtmlEngine($this->invitation);

        $reflection = new \ReflectionClass($engine);
        $method = $reflection->getMethod('getPaymentMeta');
        $method->setAccessible(true);

        $result = $method->invoke($engine);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('invoice_id', $result);
        $this->assertArrayHasKey('contact_email', $result);
    }
}

