<?php

namespace Vitorccs\LaravelCsv\Tests\Data\Imports\NoHeadings;

use Vitorccs\LaravelCsv\Concerns\Importables\FromContents;
use Vitorccs\LaravelCsv\Concerns\Importables\Importable;

class FromContentsImportAlt implements FromContents
{
    use Importable, FromImportTrait;

    public function __construct(private ?int $limit = null)
    {
    }

    public function limit(): ?int
    {
        return $this->limit;
    }

    public function contents(): string
    {
        return "'a 1'|'b 1'|'c 1'\na2|b2|c2\na3|b3|c3\na4|b4|c4\na5|b5|c5\na6|b6|c6\na7|b7|c7\na8|b8|c8\na9|b9|c9\na10|b10|c10";
    }

    public function csvDelimiter(): string
    {
        return '|';
    }

    public function csvEnclosure(): string
    {
        return "'";
    }
}