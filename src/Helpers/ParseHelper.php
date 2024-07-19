<?php

namespace Vitorccs\LaravelCsv\Helpers;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;

class ParseHelper
{
    /**
     * @param string $date
     * @param string $format
     * @return Carbon|null
     */
    public static function toCarbon(string $date,
                                    string $format): ?Carbon
    {
        try {
            return Carbon::createFromFormat($format, $date) ?: null;
        } catch (InvalidFormatException $e) {
            return null;
        }
    }

    /**
     * @param string $number
     * @param string $decimalSep
     * @param string $thousandSep
     * @return float|null
     */
    public static function toFloat(string $number,
                                   string $decimalSep,
                                   string $thousandSep): ?float
    {
        preg_match("/[\d{$decimalSep}{$thousandSep}]+/", $number, $matches);

        if (empty($matches)) return null;

        $number = reset($matches);
        $number = str_replace($thousandSep, '', $number);
        $number = str_replace($decimalSep, '.', $number);

        return floatval($number);
    }
}