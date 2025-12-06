<?php

namespace Tests\DataProviders;

use PHPUnit\Framework\TestCase;
use App\DataProviders\Domains;

class DomainsTest extends TestCase
{
    public function test_get_domains_returns_array()
    {
        $domains = Domains::getDomains();

        $this->assertIsArray($domains); // Ensure it returns an array
        $this->assertNotEmpty($domains); // Ensure it's not empty

        // Check that a few known domains exist
        $expectedDomains = [
            'yahoo.com',
            'gmail.com',
            'fastmail.com',
            'wowmail.com',
        ];

        foreach ($expectedDomains as $domain) {
            $this->assertContains($domain, $domains);
        }
    }

    public function test_all_domains_are_strings()
    {
        $domains = Domains::getDomains();

        foreach ($domains as $domain) {
            $this->assertIsString($domain);
        }
    }
}

