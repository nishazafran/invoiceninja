<?php
namespace Tests\Utils\HostedPDF;

use Tests\TestCase;
use App\Utils\HostedPDF\NinjaPdf;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\StreamInterface;
use Illuminate\Support\Facades\Config;
use Mockery;

class NinjaPdfTest extends TestCase
{
    public function test_build_returns_pdf_content()
    {
        // Set config
        Config::set('ninja.app_url', 'https://example.test');

        // Mock response stream implementing StreamInterface
        $stream = Mockery::mock(StreamInterface::class);
        $stream->shouldReceive('getContents')
            ->once()
            ->andReturn('PDF_BINARY');

        // Mock Guzzle Response
        $response = Mockery::mock(Response::class);
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn($stream);

        // Mock Guzzle Client (replace new Client inside NinjaPdf)
        $client = Mockery::mock('overload:' . Client::class);
        $client->shouldReceive('post')
            ->once()
            ->with('https://pdf.invoicing.co/api/', [
                'json' => ['html' => '<h1>Hello</h1>'],
            ])
            ->andReturn($response);

        $pdf = new NinjaPdf();
        $result = $pdf->build('<h1>Hello</h1>');

        $this->assertEquals('PDF_BINARY', $result);
    }
}

