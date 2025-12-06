<?php

namespace Tests\Notifications;

use Tests\TestCase;
use App\Notifications\Admin\EntityViewedNotification;
use Mockery;


/* ---- STUB HELPERS ----*/

if (!function_exists('ctrans')) {
    function ctrans($text, $replace = [], $locale = null) {
        return "translated_{$text}";
    }
}

class ClientStub2 {
    public function getMergedSettings() {
        return (object)[
            'currency_symbol' => '$',
            'thousand_separator' => ',',
            'decimal_separator' => '.',
            'number_of_decimals' => 2,
        ];
    }
}

class ContactStub2 {
    public $client;

    public function __construct() {
        $this->client = new ClientStub2();
    }

    public function present() { return $this; }
    public function name() { return 'Test Contact'; }
}

class EntityStub2 {
    public $amount = 150;
    public $client;
    public $number = 'INV-001';

    public function __construct() {
        $this->client = new ClientStub2();
    }
}

class CompanyStub2 {
    public function present() { return $this; }
    public function logo() { return 'https://logo.url/logo.png'; }
}

class InvitationStub2 {
    public $contact;
    public $company;
    public $invoice;
    public $viewed_date = '2025-01-01 00:00:00';

    public function __construct() {
        $this->contact = new ContactStub2();
        $this->company = new CompanyStub2();
        $this->invoice = new EntityStub2();   // For entity_name = "invoice"
    }

    public function getAdminLink() {
        return 'https://admin.link';
    }
}


 // ---- ACTUAL TESTS ----
class EntityViewedNotificationTest extends TestCase
{
    public function test_constructor_sets_properties()
    {
        $invitation = new InvitationStub2();
        $notification = new EntityViewedNotification($invitation, 'invoice', true);

        $ref = new \ReflectionClass($notification);

        $entityName = $ref->getProperty('entity_name');
        $entityName->setAccessible(true);
        $this->assertEquals('invoice', $entityName->getValue($notification));

        $entity = $ref->getProperty('entity');
        $entity->setAccessible(true);
        $this->assertEquals($invitation->invoice, $entity->getValue($notification));

        $isSystem = $ref->getProperty('is_system');
        $isSystem->setAccessible(true);
        $this->assertTrue($isSystem->getValue($notification));

        $settings = $ref->getProperty('settings');
        $settings->setAccessible(true);
        $this->assertNotEmpty($settings->getValue($notification));
    }

    public function test_via_returns_empty_array_by_default()
    {
        $invitation = new InvitationStub2();
        $notification = new EntityViewedNotification($invitation, 'invoice');

        $this->assertEquals([], $notification->via(null));
    }

    public function test_toArray_returns_array()
    {
        $invitation = new InvitationStub2();
        $notification = new EntityViewedNotification($invitation, 'invoice');

        $this->assertIsArray($notification->toArray(null));
    }
    
    public function test_toMail_returns_null()
{
    $invitation = new InvitationStub2();
    $notification = new \App\Notifications\Admin\EntityViewedNotification(
        $invitation,
        'invoice'
    );

    $result = $notification->toMail(null);

    // Empty function = returns null
    $this->assertNull($result);
}

    
}

