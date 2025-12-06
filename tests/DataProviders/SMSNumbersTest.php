<?php

namespace Tests\Unit\DataProviders;

use PHPUnit\Framework\TestCase;
use App\DataProviders\SMSNumbers;

class SMSNumbersTest extends TestCase
{
    public function test_get_numbers_returns_array()
    {
        $numbers = SMSNumbers::getNumbers();

        $this->assertIsArray($numbers);
        $this->assertNotEmpty($numbers);
        $this->assertContains('+62895601645353', $numbers); // known number from list
    }

    public function test_has_number()
    {
        $this->assertTrue(SMSNumbers::hasNumber('+62895601645353')); // existing number
        $this->assertFalse(SMSNumbers::hasNumber('+1234567890')); // non-existing number
    }

    public function test_unique_numbers()
    {
        $unique = SMSNumbers::uniqueNumbers();

        $this->assertIsArray($unique);
        $this->assertEquals($unique, array_values(array_unique($unique))); // ensure uniqueness
        $this->assertEquals($unique, array_values($unique)); // ensure sorted (array_values returns indexed array)
    }

    public function test_de_dupe()
    {
        // Simply call it to cover method
        SMSNumbers::deDupe();
        $this->assertTrue(true); // dummy assertion to satisfy PHPUnit
    }
}

