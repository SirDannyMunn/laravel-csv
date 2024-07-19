<?php

namespace Vitorccs\LaravelCsv\Tests\Data\Imports;

use Vitorccs\LaravelCsv\Concerns\Importables\FromContents;
use Vitorccs\LaravelCsv\Concerns\Importables\Importable;
use Vitorccs\LaravelCsv\Concerns\WithMapping;

class WithMappingImport implements FromContents, WithMapping
{
    use Importable;

    public function contents(): string
    {
        return "abc,42,1\ndef,78,0\n";
    }

    public function expected(): array
    {
        return [
            ['ABC', 42, 'Active'],
            ['DEF', 78, 'Inactive'],
        ];
    }

    public function map($row): array
    {
        return [
            mb_strtoupper($row[0]),
            intval($row[1]),
            intval($row[2]) ? 'Active' : 'Inactive'
        ];
    }
}
