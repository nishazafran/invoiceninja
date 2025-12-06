<?php

namespace Tests\Utils;

use App\Utils\TempFile;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class TempFileTest extends TestCase
{
    public function test_path_creates_temp_file_and_returns_path()
    {
        $url = __DIR__ . '/testfile.txt';
        file_put_contents($url, 'Hello World');

        $tempPath = TempFile::path($url);

        $this->assertFileExists($tempPath);
        $this->assertEquals('Hello World', file_get_contents($tempPath));

        // Cleanup
        unlink($url);
        unlink($tempPath);
    }

    public function test_filePath_creates_temp_file_with_content()
    {
        $data = 'Test Content';
        $filename = 'myfile.txt';

        $path = TempFile::filePath($data, $filename);

        $this->assertFileExists($path);
        $this->assertEquals($data, file_get_contents($path));

        // Cleanup
        unlink($path);
        rmdir(dirname($path));
    }

    public function test_UploadedFileFromBase64_calls_fclose()
    {
        $string = base64_encode('test base64 content');
        $base64 = "data:text/plain;base64,{$string}";

        $fcloseCalled = false;
        app()->terminating(function () use (&$fcloseCalled) {
            $fcloseCalled = true;
        });

        $file = TempFile::UploadedFileFromBase64($base64, 'base64.txt', 'text/plain');

        $this->assertInstanceOf(UploadedFile::class, $file);
        $this->assertEquals('base64.txt', $file->getClientOriginalName());
        $this->assertEquals('text/plain', $file->getMimeType());
        $this->assertEquals('test base64 content', file_get_contents($file->getPathname()));
        $this->assertFalse($fcloseCalled, 'fclose() callback was not executed');
    }

    public function test_UploadedFileFromRaw_calls_fclose()
    {
        $data = 'raw content here';

        $fcloseCalled = false;
        app()->terminating(function () use (&$fcloseCalled) {
            $fcloseCalled = true;
        });

        $file = TempFile::UploadedFileFromRaw($data, 'raw.txt', 'text/plain');

        $this->assertInstanceOf(UploadedFile::class, $file);
        $this->assertEquals('raw.txt', $file->getClientOriginalName());
        $this->assertEquals('text/plain', $file->getMimeType());
        $this->assertEquals($data, file_get_contents($file->getPathname()));
        $this->assertFalse($fcloseCalled, 'fclose() callback was not executed');
    }

    public function test_UploadedFileFromUrl_calls_fclose()
    {
        $url = __DIR__ . '/remote.txt';
        file_put_contents($url, 'remote file content');

        $fcloseCalled = false;
        app()->terminating(function () use (&$fcloseCalled) {
            $fcloseCalled = true;
        });

        $file = TempFile::UploadedFileFromUrl($url, 'remote.txt', 'text/plain');

        $this->assertInstanceOf(UploadedFile::class, $file);
        $this->assertEquals('remote.txt', $file->getClientOriginalName());
        $this->assertEquals('text/plain', $file->getMimeType());
        $this->assertEquals('remote file content', file_get_contents($file->getPathname()));
        $this->assertFalse($fcloseCalled, 'fclose() callback was not executed');

        unlink($url);
    }
}

