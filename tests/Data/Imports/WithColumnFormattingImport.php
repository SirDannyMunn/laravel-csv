<?php

namespace Vitorccs\LaravelCsv\Tests\Data\Imports;

use Carbon\Carbon;
use Vitorccs\LaravelCsv\Concerns\Importables\FromContents;
use Vitorccs\LaravelCsv\Concerns\Importables\Importable;
use Vitorccs\LaravelCsv\Concerns\WithColumnFormatting;
use Vitorccs\LaravelCsv\Enum\CellFormat;

class WithColumnFormattingImport implements FromContents, WithColumnFormatting
{
    use Importable;

    public function contents(): string
    {
        // 2nd line must ignore carbon parse since they are in an unexpected format
        return "1,2:3,2021_02_03,31/12/2021 12_34_56\n\"2\",\"5#300:91\",\"2021-02-03\",\"2021-12-31 23:15\"";
    }

    public function formatDate(): string
    {
        return 'Y_m_d';
    }

    public function formatDateTime(): string
    {
        return 'd/m/Y H_i_s';
    }

    public function decimalSeparator(): string
    {
        return ':';
    }

    public function thousandSeparator(): string
    {
        return '#';
    }

    public function columnFormats(): array
    {
        return [
            'A' => CellFormat::INTEGER,
            'B' => CellFormat::DECIMAL,
            'C' => CellFormat::DATE,
            'D' => CellFormat::DATETIME
        ];
    }

    public function expected(): array
    {
        return [
            [1, 2.30, Carbon::parse('2021-02-03'), Carbon::parse('2021-12-31 12:34:56')],
            [2, 5300.91, '2021-02-03', '2021-12-31 23:15']
        ];
    }
}
