<?php

namespace Tests\Unit\Utils;

use App\Utils\Number;
use PHPUnit\Framework\TestCase;

class NumberTest extends TestCase
{
    public function test_roundValue()
    {
        $this->assertEquals(2.35, Number::roundValue(2.345, 2));
        $this->assertEquals(-2.35, Number::roundValue(-2.345, 2));
    }

    public function test_formatValue()
    {
        $currency = (object)[
            'thousand_separator' => ',',
            'decimal_separator' => '.',
            'precision' => 2
        ];

        $this->assertEquals('1,234.57', Number::formatValue(1234.567, $currency));
    }

    public function test_formatValueNoTrailingZeroes()
    {
        $currency = new class {
            public $thousand_separator = ',';
            public $decimal_separator = '.';
            public function precision() { return 2; }
        };
        $entity = new class($currency) {
            public $currencyObj;
            public $country;
            public function __construct($c) { $this->currencyObj = $c; $this->country = (object)[]; }
            public function currency() { return $this->currencyObj; }
        };

        $this->assertEquals('1,234.56789', Number::formatValueNoTrailingZeroes(1234.56789, $entity));
    }

    public function test_parseFloat_variants()
    {
        $this->assertEquals(1234.56, Number::parseFloat('1,234.56'));
        $this->assertEquals(1234.56, Number::parseFloat2('1,234.56'));
        $this->assertEquals(1234.56, Number::parseStringFloat('1,234.56'));
        $this->assertEquals(1234.56, Number::parseFloatXX('1,234.56'));
        $this->assertEquals(0, Number::parseFloat(null));
        $this->assertEquals(0, Number::parseFloatXX(null));
    }

    public function test_formatMoney()
    {
        $currency = (object)[
            'thousand_separator' => ',',
            'decimal_separator' => '.',
            'precision' => 2,
            'symbol' => '$',
            'code' => 'USD',
            'swap_currency_symbol' => false
        ];

        $entity = new class($currency) {
            public $currencyObj;
            public $country;
            public function __construct($c) { $this->currencyObj = $c; $this->country = (object)[]; }
            public function currency() { return $this->currencyObj; }
            public function getSetting($key) { return false; }
        };

        $this->assertEquals('$1,234.57', Number::formatMoney(1234.57, $entity));
        $this->assertEquals('$1,234.57', Number::formatMoneyNoRounding(1234.57, $entity));
    }
}

