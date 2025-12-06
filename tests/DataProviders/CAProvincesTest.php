<?php

namespace Tests\Unit\DataProviders;

use App\DataProviders\CAProvinces;
use PHPUnit\Framework\TestCase;

class CAProvincesTest extends TestCase
{
    public function test_get_name_returns_correct_value()
    {
        // Exact abbreviation
        $this->assertSame('Ontario', CAProvinces::getName('ON'));

        // Test all abbreviations exist
        foreach (CAProvinces::get() as $abbr => $name) {
            $this->assertSame($name, CAProvinces::getName($abbr));
        }
    }

    public function test_get_returns_all_provinces()
    {
        $provinces = CAProvinces::get();

        $this->assertIsArray($provinces);
        $this->assertArrayHasKey('ON', $provinces);
        $this->assertSame('Quebec', $provinces['QC']);
    }

    public function test_get_abbreviation_returns_correct_value()
    {
        // Exact capitalization
        $this->assertSame('ON', CAProvinces::getAbbreviation('Ontario'));

        // Lowercase input
        $this->assertSame('AB', CAProvinces::getAbbreviation('alberta'));

        // Multiple word province names
        $this->assertSame('NL', CAProvinces::getAbbreviation('newfoundland and Labrador'));

        // Non-existent province should return false
        $this->assertFalse(CAProvinces::getAbbreviation('Atlantis'));
    }
}

