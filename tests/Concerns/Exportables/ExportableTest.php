<?php

namespace Vitorccs\LaravelCsv\Tests\Concerns\Exportables;

use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Vitorccs\LaravelCsv\Concerns\Exportables\Exportable;
use Vitorccs\LaravelCsv\Facades\CsvExporter;
use Vitorccs\LaravelCsv\Tests\Data\Helpers\FakerHelper;
use Vitorccs\LaravelCsv\Tests\TestCase;

class ExportableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->export = new class {
            use Exportable;
        };
    }

    public function test_count()
    {
        $count = 100;

        CsvExporter::shouldReceive('count')
            ->once()
            ->andReturns($count);

        $this->assertEquals($count, $this->export->count());
    }

    public function test_to_array()
    {
        $array = [['a', 'b', 'c']];

        CsvExporter::shouldReceive('toArray')
            ->once()
            ->andReturns($array);

        $this->assertEquals($array, $this->export->toArray());
    }

    public function test_store()
    {
        CsvExporter::shouldReceive('store')
            ->once()
            ->andReturns($this->filename);

        $this->assertEquals($this->filename, $this->export->store($this->filename));
    }

    public function test_download()
    {
        $mock = \Mockery::mock(StreamedResponse::class);

        CsvExporter::shouldReceive('download')
            ->once()
            ->andReturns($mock);

        $this->assertEquals($mock, $this->export->download());
    }

    public function test_steam()
    {
        $mock = $this->getMockBuilder(\SplTempFileObject::class)
            ->disableOriginalConstructor();

        CsvExporter::shouldReceive('stream')
            ->once()
            ->andReturns($mock);

        $this->assertEquals($mock, $this->export->stream());
    }

    public function test_queue()
    {
        $mock = \Mockery::mock(PendingDispatch::class);

        CsvExporter::shouldReceive('queue')
            ->once()
            ->andReturns($mock);

        $this->assertEquals($mock, $this->export->queue());
    }

    public function test_filename()
    {
        $filename = FakerHelper::get()->word();

        $this->assertEquals($filename, $this->export->getFilename($filename));

        $filename = $this->export->getFilename();
        $filename = preg_replace('/\..+$/', '', $filename);

        $this->assertTrue(Str::isUuid($filename));
    }
}
