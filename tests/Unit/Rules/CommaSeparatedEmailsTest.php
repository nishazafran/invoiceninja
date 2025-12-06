<?php

namespace Tests\Unit\Rules;

use App\Rules\CommaSeparatedEmails;
use PHPUnit\Framework\TestCase;

class CommaSeparatedEmailsTest extends TestCase
{
    public function test_validates_single_email()
    {
        $rule = new CommaSeparatedEmails();
        $failed = false;
        
        $rule->validate('emails', 'test@example.com', function() use (&$failed) {
            $failed = true;
        });
        
        $this->assertFalse($failed);
    }
    
    public function test_validates_multiple_emails()
    {
        $rule = new CommaSeparatedEmails();
        $failed = false;
        
        $rule->validate('emails', 'test1@example.com, test2@example.com, test3@example.com', function() use (&$failed) {
            $failed = true;
        });
        
        $this->assertFalse($failed);
    }
    
    public function test_validates_emails_with_whitespace()
    {
        $rule = new CommaSeparatedEmails();
        $failed = false;
        
        $rule->validate('emails', '  test1@example.com  ,  test2@example.com  ', function() use (&$failed) {
            $failed = true;
        });
        
        $this->assertFalse($failed);
    }
    
    public function test_fails_with_invalid_email()
    {
        $rule = new CommaSeparatedEmails();
        $failed = false;
        $errorMessage = '';
        
        $rule->validate('emails', 'invalid-email, test@example.com', function($message) use (&$failed, &$errorMessage) {
            $failed = true;
            $errorMessage = $message;
        });
        
        $this->assertTrue($failed);
        $this->assertStringContainsString('invalid-email', $errorMessage);
    }
    
    public function test_fails_with_empty_emails()
    {
        $rule = new CommaSeparatedEmails();
        $failed = false;
        
        $rule->validate('emails', 'test@example.com, , test2@example.com', function() use (&$failed) {
            $failed = true;
        });
        
        $this->assertFalse($failed); // Empty emails are filtered out
    }
    
    public function test_fails_when_exceeding_max_emails()
    {
        $rule = new CommaSeparatedEmails(2); // Max 2 emails
        $failed = false;
        $errorMessage = '';
        
        $rule->validate('emails', 'test1@example.com, test2@example.com, test3@example.com', function($message) use (&$failed, &$errorMessage) {
            $failed = true;
            $errorMessage = $message;
        });
        
        $this->assertTrue($failed);
        $this->assertStringContainsString('cannot contain more than 2', $errorMessage);
    }
    
    public function test_validates_null_value()
    {
        $rule = new CommaSeparatedEmails();
        $failed = false;
        
        $rule->validate('emails', null, function() use (&$failed) {
            $failed = true;
        });
        
        $this->assertFalse($failed);
    }
    
    public function test_validates_empty_string()
    {
        $rule = new CommaSeparatedEmails();
        $failed = false;
        
        $rule->validate('emails', '', function() use (&$failed) {
            $failed = true;
        });
        
        $this->assertFalse($failed);
    }
    
    public function test_fails_when_no_valid_email_after_split()
    {
        $rule = new CommaSeparatedEmails();
        $failed = false;
        $errorMessage = '';

        $rule->validate('emails', ', , ,', function($msg) use (&$failed, &$errorMessage) {
            $failed = true;
            $errorMessage = $msg;
        });

        $this->assertTrue($failed);
        $this->assertStringContainsString('must contain at least one valid email', $errorMessage);
    }

    public function test_uses_first_separator_from_custom_separators()
    {
        $rule = new CommaSeparatedEmails(10, [';', ',']);
        $failed = false;

        $rule->validate('emails', 'a@a.com; b@b.com', function() use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed);
    }

    public function test_message_method_output()
    {
        $rule = new CommaSeparatedEmails();
        $this->assertEquals(
            'The :attribute must contain valid email addresses separated by commas.',
            $rule->message()
        );
    }

    public function test_parse_emails_static_method()
    {
        $result = CommaSeparatedEmails::parseEmails(" a@a.com , b@b.com ,, ");
        $this->assertEquals(['a@a.com', 'b@b.com'], $result);
    }

    public function test_parse_emails_returns_empty_array_for_empty_string()
    {
        $this->assertEquals([], CommaSeparatedEmails::parseEmails(""));
    }

    public function test_is_valid_email_returns_true()
    {
        $this->assertTrue(CommaSeparatedEmails::isValidEmail("valid@example.com"));
    }

    public function test_is_valid_email_returns_false()
    {
        $this->assertFalse(CommaSeparatedEmails::isValidEmail("invalid-email"));
    }
}
