<?php

namespace Vitorccs\LaravelCsv\Tests\Data\Imports\NoHeadings;

use Vitorccs\LaravelCsv\Concerns\Importables\FromContents;
use Vitorccs\LaravelCsv\Concerns\Importables\Importable;

class FromContentsImport implements FromContents
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