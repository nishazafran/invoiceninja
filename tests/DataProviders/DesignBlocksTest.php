<?php

namespace Tests\Unit\DataProviders;

use App\DataProviders\DesignBlocks;
use PHPUnit\Framework\TestCase;

class DesignBlocksTest extends TestCase
{
    public function test_can_instantiate_and_assign_properties()
    {
        $includes = 'include-css';
        $header = '<h1>Header</h1>';
        $body = '<p>Body</p>';
        $footer = '<footer>Footer</footer>';

        $block = new DesignBlocks($includes, $header, $body, $footer);

        $this->assertSame($includes, $block->includes);
        $this->assertSame($header, $block->header);
        $this->assertSame($body, $block->body);
        $this->assertSame($footer, $block->footer);

        // Also test default values when no arguments provided
        $defaultBlock = new DesignBlocks();

        $this->assertSame('', $defaultBlock->includes);
        $this->assertSame('', $defaultBlock->header);
        $this->assertSame('', $defaultBlock->body);
        $this->assertSame('', $defaultBlock->footer);
    }
}

