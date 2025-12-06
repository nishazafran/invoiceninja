<?php

namespace Tests\Unit\Transformers;

use App\Models\ClientContact;
use App\Transformers\ClientContactLoginTransformer;
use Tests\TestCase;

class ClientContactLoginTransformerTest extends TestCase
{
    private ClientContactLoginTransformer $transformer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transformer = new ClientContactLoginTransformer();
    }

    /** @test */
    public function test_transform_returns_correct_array()
    {
        $contact = new ClientContact();
        $contact->id = 1;
        $contact->first_name = 'John';
        $contact->last_name = 'Doe';
        $contact->email = 'john@example.com';
        $contact->created_at = 1699999999;
        $contact->updated_at = 1700000000;
        $contact->deleted_at = null;
        $contact->is_primary = true;
        $contact->is_locked = false;
        $contact->phone = '1234567890';
        $contact->custom_value1 = 'custom1';
        $contact->custom_value2 = 'custom2';
        $contact->custom_value3 = '';
        $contact->custom_value4 = '';
        $contact->token = 'abc123';

        $result = $this->transformer->transform($contact);

        $this->assertIsArray($result);
        $this->assertEquals($this->transformer->encodePrimaryKey(1), $result['id']);
        $this->assertEquals('John', $result['first_name']);
        $this->assertEquals('Doe', $result['last_name']);
        $this->assertEquals('john@example.com', $result['email']);
        $this->assertEquals(1699999999, $result['created_at']);
        $this->assertEquals(1700000000, $result['updated_at']);
        $this->assertEquals(0, $result['archived_at']); // deleted_at is null
        $this->assertTrue($result['is_primary']);
        $this->assertFalse($result['is_locked']);
        $this->assertEquals('1234567890', $result['phone']);
        $this->assertEquals('custom1', $result['custom_value1']);
        $this->assertEquals('custom2', $result['custom_value2']);
        $this->assertEquals('', $result['custom_value3']);
        $this->assertEquals('', $result['custom_value4']);
        $this->assertEquals('abc123', $result['token']);
    }

    /** @test */
    public function test_transform_handles_empty_values()
    {
        $contact = new ClientContact();
        $contact->id = 2;
        $contact->first_name = null;
        $contact->last_name = null;
        $contact->email = null;
        $contact->created_at = null;
        $contact->updated_at = null;
        $contact->deleted_at = null;
        $contact->is_primary = null;
        $contact->is_locked = null;
        $contact->phone = null;
        $contact->custom_value1 = null;
        $contact->custom_value2 = null;
        $contact->custom_value3 = null;
        $contact->custom_value4 = null;
        $contact->token = null;

        $result = $this->transformer->transform($contact);

        $this->assertEquals($this->transformer->encodePrimaryKey(2), $result['id']);
        $this->assertEquals('', $result['first_name']);
        $this->assertEquals('', $result['last_name']);
        $this->assertEquals('', $result['email']);
        $this->assertEquals(0, $result['created_at']);
        $this->assertEquals(0, $result['updated_at']);
        $this->assertEquals(0, $result['archived_at']);
        $this->assertFalse($result['is_primary']);
        $this->assertFalse($result['is_locked']);
        $this->assertEquals('', $result['phone']);
        $this->assertEquals('', $result['custom_value1']);
        $this->assertEquals('', $result['custom_value2']);
        $this->assertEquals('', $result['custom_value3']);
        $this->assertEquals('', $result['custom_value4']);
        $this->assertEquals('', $result['token']);
    }
}

