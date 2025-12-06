<?php

namespace Tests\Unit\Transformers;

use Tests\TestCase;
use App\Models\RecurringQuoteInvitation;
use App\Transformers\RecurringQuoteInvitationTransformer;

class RecurringQuoteInvitationTransformerTest extends TestCase
{
    /** @test */
    public function it_transforms_recurring_quote_invitation()
    {
        // Create a mock of RecurringQuoteInvitation
        $invitation = $this->getMockBuilder(RecurringQuoteInvitation::class)
            ->onlyMethods(['getLink'])
            ->getMock();

        $invitation->id = 1;
        $invitation->client_contact_id = 2;
        $invitation->key = 'ABC123';
        $invitation->sent_date = '2025-12-01';
        $invitation->viewed_date = '2025-12-02';
        $invitation->opened_date = '2025-12-03';
        $invitation->updated_at = time();
        $invitation->deleted_at = null;
        $invitation->created_at = time() - 3600;
        $invitation->email_status = 'sent';
        $invitation->email_error = '';

        // Mock getLink() to return a URL
        $invitation->method('getLink')->willReturn('https://example.com/quote/ABC123');

        $transformer = new RecurringQuoteInvitationTransformer();
        $data = $transformer->transform($invitation);

        // Assert hashed ID instead of raw ID
        $this->assertEquals($transformer->encodePrimaryKey(1), $data['id']);
        $this->assertEquals($transformer->encodePrimaryKey(2), $data['client_contact_id']);
        $this->assertEquals('ABC123', $data['key']);
        $this->assertEquals('https://example.com/quote/ABC123', $data['link']);
        $this->assertEquals('2025-12-01', $data['sent_date']);
        $this->assertEquals('2025-12-02', $data['viewed_date']);
        $this->assertEquals('2025-12-03', $data['opened_date']);
        $this->assertEquals((int) $invitation->updated_at, $data['updated_at']);
        $this->assertEquals((int) $invitation->deleted_at, $data['archived_at']);
        $this->assertEquals((int) $invitation->created_at, $data['created_at']);
        $this->assertEquals('sent', $data['email_status']);
        $this->assertEquals('', $data['email_error']);
    }
}

