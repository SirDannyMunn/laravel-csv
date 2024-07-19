<?php

namespace Vitorccs\LaravelCsv\Tests\Data\Imports\NoHeadings;

use Illuminate\Support\Facades\Storage;
use Vitorccs\LaravelCsv\Concerns\Importables\FromDisk;
use Vitorccs\LaravelCsv\Concerns\Importables\Importable;

class FromDiskImport implements FromDisk
{
    use Importable, FromImportTrait;

    private string $filename;

    public function __construct(private ?int $limit = null)
    {
        $this->filename = uniqid();

        Storage::put($this->filename, $this->contents());
    }

    public function delete(): void
    {
        Storage::delete($this->filename);
    }

    public function limit(): ?int
    {
        return $this->limit;
    }

    public function filename(): string
    {
        return $this->filename;
    }

    public function disk(): ?string
    {
        return null;
    }
}