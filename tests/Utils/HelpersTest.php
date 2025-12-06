<?php

namespace Tests\Utils;

use App\Models\Client;
use App\Models\Company;
use App\Utils\Helpers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use stdClass;
use Carbon\Carbon;

class HelpersTest extends TestCase
{
    use RefreshDatabase;

    public function test_sharedEmailVariables_returns_defaults_when_client_is_null()
    {
        $result = Helpers::sharedEmailVariables(null);

        $this->assertEquals('', $result['signature']);
        $this->assertInstanceOf(stdClass::class, $result['settings']);
        $this->assertTrue($result['whitelabel']);
        $this->assertEquals('', $result['company']);
    }


    public function test_sharedEmailVariables_returns_values_when_client_exists()
    {
        $company = new Company();
        $company->name = 'TestCo';
        $account = new class {
            public function isPaid() { return true; }
        };
        $company->account = $account;

        $client = new Client();
        $client->company = $company;
        $client->getMergedSettings = (object)['email_signature' => 'Best regards'];

        $result = Helpers::sharedEmailVariables($client, $client->getMergedSettings);

        $this->assertEquals('Best regards', $result['signature']);
        $this->assertEquals($client->getMergedSettings, $result['settings']);
        $this->assertTrue($result['whitelabel']);
        $this->assertEquals($company, $result['company']);
    }

    public function test_formatCustomFieldValue_returns_date_translation()
    {
        $helpers = new Helpers();

        $custom_fields = (object)['custom1' => 'date'];
        $client = new class {
            public function date_format() { return 'Y-m-d'; }
            public function locale() { return 'en'; }
        };

        $value = '2025-12-05';
        $result = $helpers->formatCustomFieldValue($custom_fields, 'custom1', $value, $client);

        $this->assertEquals('2025-12-05', $result);
    }


    public function test_formatCustomFieldValue_returns_switch_yes_or_no()
    {
        $helpers = new Helpers();

        $custom_fields = (object)['custom1' => 'switch'];

        $resultYes = $helpers->formatCustomFieldValue($custom_fields, 'custom1', 'yes');
        $resultNo = $helpers->formatCustomFieldValue($custom_fields, 'custom1', 'no');

        $this->assertEquals('Yes', $resultYes);
        $this->assertEquals('No', $resultNo);
    }


    public function test_formatCustomFieldValue_returns_default_for_other_values()
    {
        $helpers = new Helpers();

        $custom_fields = (object)['custom1' => 'text'];
        $client = new class {
            public function locale() { return 'en'; }
        };

        $result = $helpers->formatCustomFieldValue($custom_fields, 'custom1', 'Hello World', $client);

        $this->assertEquals('Hello World', $result);
    }


    public function test_makeCustomField_returns_first_part_of_custom_field()
    {
        $helpers = new Helpers();
        $custom_fields = (object)['custom1' => 'Field Name|Value'];

        $result = $helpers->makeCustomField($custom_fields, 'custom1');

        $this->assertEquals('Field Name', $result);
    }


    public function test_makeCustomField_returns_empty_when_field_missing()
    {
        $helpers = new Helpers();
        $custom_fields = (object)[];

        $result = $helpers->makeCustomField($custom_fields, 'custom1');

        $this->assertEquals('', $result);
    }


    public function test_processReservedKeywords_returns_empty_when_value_null()
    {
        $client = new class {
            public function locale() { return 'en'; }
            public function timezone() { return (object)['name' => 'UTC']; }
        };

        $this->assertEquals('', Helpers::processReservedKeywords(null, $client));
    }


    public function test_processReservedKeywords_returns_value_when_no_keywords()
    {
        $client = new class {
            public function locale() { return 'en'; }
            public function timezone() { return (object)['name' => 'UTC']; }
        };

        $value = 'No keywords here';
        $this->assertEquals($value, Helpers::processReservedKeywords($value, $client));
    }


    public function test_processReservedKeywords_replaces_basic_keywords()
    {
        $client = new class {
            public function locale() { return 'en'; }
            public function timezone() { return (object)['name' => 'UTC']; }
            public function date_format() { return 'F'; }

        };

        $value = 'Today is :MONTH:YEAR';
        $expected = 'Today is ' . Carbon::now()->translatedFormat('F') . Carbon::now()->year;

        $this->assertEquals($expected, Helpers::processReservedKeywords($value, $client));
    }

    public function test_processReservedKeywords_handles_plus_month_operation()
    {
        $client = new class {
            public function locale() { return 'en'; }
            public function timezone() { return (object)['name' => 'UTC']; }
            public function date_format() { return 'F'; }

        };

        $current = Carbon::create(2025, 12, 5);
        $value = 'Next month is :MONTH+1';
        $expected = 'Next month is ' . $current->copy()->addMonth()->translatedFormat('F');

        $this->assertEquals($expected, Helpers::processReservedKeywords($value, $client, $current));
    }


    public function test_resolveFont_returns_google_font_array()
    {
        $result = Helpers::resolveFont('Roboto');

        $this->assertEquals('Roboto', $result['name']);
        $this->assertStringContainsString('Roboto', $result['url']);
    }


    public function test_resolveFont_returns_default_when_font_empty()
    {
        $result = Helpers::resolveFont('');

        $this->assertEquals('Arial', $result['name']);
        $this->assertEquals('', $result['url']);
    }
    
    
}

