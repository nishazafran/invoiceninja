<?php

namespace Tests\Utils\PhantomJS;

use App\Utils\PhantomJS\Phantom;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class PhantomTest extends TestCase
{
    public function test_convert_html_to_pdf_returns_pdf_response()
    {
        // Set dummy PhantomJS key for testing
        Config::set('ninja.phantomjs_key', 'DUMMY_KEY');

        $html = '<h1>Hello PDF</h1>';

        $phantom = new Phantom();
        $response = $phantom->convertHtmlToPdf($html);

        // Assert instance type
        $this->assertEquals('Illuminate\Http\Response', get_class($response));

        // Assert content type header
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));

        // Assert response content is a string
        $this->assertIsString($response->getContent());
    }
}

