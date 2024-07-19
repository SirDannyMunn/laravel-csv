<?php

namespace Vitorccs\LaravelCsv\Tests\Data\Imports\WithHeadings;

use Vitorccs\LaravelCsv\Concerns\Importables\FromFile;
use Vitorccs\LaravelCsv\Concerns\Importables\Importable;
use Vitorccs\LaravelCsv\Concerns\WithHeadings;

class FromFileImport implements FromFile, WithHeadings
{
    use Importable, FromImportTrait;

    private string $uniqId;

    public function __construct(private ?int $limit = null)
    {
        $this->uniqId = uniqid();

        file_put_contents($this->filename(), $this->contents()) or throw new \RuntimeException('Unable to write file');
    }

    public function limit(): ?int
    {
        return $this->limit;
    }

    public function delete(): void
    {
        unlink($this->filename());
    }

    public function filename(): string
    {
        return sprintf('%s/%s.csv', realpath(__DIR__ . '/../../Storage'), $this->uniqId);
    }
}