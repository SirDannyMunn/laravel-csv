<?php

namespace Vitorccs\LaravelCsv\Tests\Data\Imports\WithHeadings;

use Vitorccs\LaravelCsv\Concerns\Importables\FromContents;
use Vitorccs\LaravelCsv\Concerns\Importables\Importable;
use Vitorccs\LaravelCsv\Concerns\WithHeadings;

class FromContentsImport implements FromContents, WithHeadings
{
    use Importable, FromImportTrait;

    public function __construct(private ?int $limit = null)
    {
    }

    public function limit(): ?int
    {
        return $this->limit;
    }
}