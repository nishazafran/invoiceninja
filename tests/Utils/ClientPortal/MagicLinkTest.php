<?php

namespace Tests\Utils\ClientPortal;

use Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Utils\ClientPortal\MagicLink;

class MagicLinkTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Fake route for testing
        Route::get('/client/magic/{magic_link}', function () {
        })->name('client.contact_magic_link');
    }

    public function test_magic_link_is_created_and_cached()
    {
    	$magicKey = 'TESTMAGICKEY123456789TESTMAGICKEY123456789TESTMAGICKEY123456789AA';

    	// Fake cache facade
    	Cache::shouldReceive('add')
    	    ->once()
    	    ->with(
    	        $magicKey,
    		        ['email' => 'client@example.com', 'company_id' => 55],
    	        600
    	    )
    	    ->andReturn(true);
	
    	$service = new \App\Utils\ClientPortal\MagicLink();
	
    	$link = $service->create('client@example.com', 55, $magicKey);
	$this->assertEquals($magicKey, $link);
    }

    public function test_magic_link_accepts_custom_redirect_url()
    {
        Str::createRandomStringsUsing(fn() => 'MAGICKEYABC');

        $url = MagicLink::create('a@b.com', 1, '/dashboard');

        Str::createRandomStringsNormally();

        $this->assertStringContainsString('magic_link/MAGICKEYABC', $url);
        $this->assertStringContainsString('redirect=%2Fdashboard', $url);
    }
}

