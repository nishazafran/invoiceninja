<?php

namespace Tests\Notifications;

use Tests\TestCase;
use App\Notifications\Admin\NewPaymentNotification;
use Illuminate\Notifications\Messages\SlackMessage;
use App\Utils\Number;

if (!function_exists('ctrans')) {
    function ctrans($text, $replace = [], $locale = null) {
        // Simple stub: you can also return a formatted string if needed
        $replacements = '';
        if (!empty($replace)) {
            foreach ($replace as $key => $value) {
                $replacements .= "$key:$value,";
            }
        }
        return "translated_{$text}" . ($replacements ? " ($replacements)" : '');
    }
}

class ClientStub {
    public function getMergedSettings() {
        return (object)[
            'thousand_separator' => ',',
            'decimal_separator' => '.',
            'currency_symbol' => '$',
            'currency_symbol_first' => true,
            'number_of_decimals' => 2
        ];
    }

    // Some parts of Number::formatMoney() call getSetting()
    public function getSetting($key, $default = null) {
        $settings = $this->getMergedSettings();
        return $settings->$key ?? $default;
    }

    public function present() { return $this; }
    public function name() { return 'Test Client'; }
    public function currency() { return 'USD'; }
}


// Company stub
class CompanyStub {
    public function present() { return $this; }
    public function logo() { return 'https://logo.url/logo.png'; }
}

// Payment stub
class PaymentStub {
    public $amount;
    public $client;
    public $invoices;

    public function __construct() {
        $this->amount = 200;
        $this->client = new ClientStub();
        $this->invoices = [
            (object)['number' => 'INV-100'],
            (object)['number' => 'INV-101'],
        ];
    }
}

class NewPaymentNotificationTest extends TestCase
{

    public function test_properties_are_set_correctly()
    {
        $payment = new PaymentStub();
        $company = new CompanyStub();
        $notification = new NewPaymentNotification($payment, $company, true);

        $reflection = new \ReflectionClass($notification);

        $propIsSystem = $reflection->getProperty('is_system');
        $propIsSystem->setAccessible(true);
        $this->assertTrue($propIsSystem->getValue($notification));

        $propPayment = $reflection->getProperty('payment');
        $propPayment->setAccessible(true);
        $this->assertSame($payment, $propPayment->getValue($notification));

        $propCompany = $reflection->getProperty('company');
        $propCompany->setAccessible(true);
        $this->assertSame($company, $propCompany->getValue($notification));
    }

    public function test_via_returns_empty_array_if_no_method_set()
    {
        $payment = new PaymentStub();
        $company = new CompanyStub();
        $notification = new NewPaymentNotification($payment, $company);
        $this->assertEquals([], $notification->via(null));
    }

    public function test_toArray_returns_array()
    {
        $payment = new PaymentStub();
        $company = new CompanyStub();
        $notification = new NewPaymentNotification($payment, $company);
        $this->assertIsArray($notification->toArray(null));
    }

    public function test_toMail_can_be_called()
    {
        $payment = new PaymentStub();
        $company = new CompanyStub();
        $notification = new NewPaymentNotification($payment, $company);

        $notification->toMail(null);
        $this->assertTrue(true); // coverage only
    }
}

