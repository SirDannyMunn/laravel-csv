<?php

namespace Vitorccs\LaravelCsv\Tests\Data\Exports;

use Carbon\Carbon;
use Vitorccs\LaravelCsv\Concerns\Exportables\Exportable;
use Vitorccs\LaravelCsv\Concerns\Exportables\FromArray;
use Vitorccs\LaravelCsv\Concerns\WithColumnFormatting;
use Vitorccs\LaravelCsv\Enum\CellFormat;

class WithColumnFormattingExport implements FromArray, WithColumnFormatting
{
    use Exportable;

    public function array(): array
    {
        // 2nd line must ignore carbon parse since they are in an unexpected format
        return [
            [1, 2.30, Carbon::parse('2021-02-03'), Carbon::parse('2021-12-31 12:34:56')],
            [2, 5300.91, Carbon::parse('2021-02-03')->toDateString(), Carbon::parse('2021-12-31 23:15:46')->toDateTimeString()]
        ];
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

    public function expected(): string
    {
        return '1,2:30,2021_02_03,"31/12/2021 12_34_56"' . "\n" .
            '2,5#300:91,2021-02-03,"2021-12-31 23:15:46"';
    }
}
