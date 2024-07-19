<?php

namespace Vitorccs\LaravelCsv\Services;

use Carbon\Carbon;
use Vitorccs\LaravelCsv\Entities\CsvConfig;
use Vitorccs\LaravelCsv\Helpers\ParseHelper;

class ParserService
{
    /**
     * @var ParserService
     */
    private ParserService $parser;

    /**
     * @var CsvConfig
     */
    public CsvConfig $config;

    /**
     * @param CsvConfig $config
     */
    public function __construct(CsvConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $date
     * @return Carbon|null
     */
    public function toCarbonDate(string $date): ?Carbon
    {
        return ParseHelper::toCarbon($date, $this->config->format_date) ?: null;
    }

    /**
     * @param string $date
     * @return Carbon|null
     */
    public function toCarbonDatetime(string $date): ?Carbon
    {
        return ParseHelper::toCarbon($date, $this->config->format_datetime) ?: null;
    }

    /**
     * @param string $decimal
     * @return float|null
     */
    public function toFloat(string $decimal): ?float
    {
        $value = ParseHelper::toFloat(
            $decimal,
            $this->config->format_number_decimal_sep,
            $this->config->format_number_thousand_sep
        );

        return $value ?: null;
    }

    /**
     * @param string $integer
     * @return int|null
     */
    public function toInteger(string $integer): ?int
    {
        $value = $this->toFloat($integer);

        return $value ? intval($value) : null;
    }
}