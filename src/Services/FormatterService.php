<?php

namespace Vitorccs\LaravelCsv\Services;

use Vitorccs\LaravelCsv\Entities\CsvConfig;
use Vitorccs\LaravelCsv\Helpers\FormatterHelper;

class FormatterService
{
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
     * @param \DateTime|string $date
     * @return string
     */
    public function date(\DateTime|string $date): string
    {
        return FormatterHelper::date($date, $this->config->format_date);
    }

    /**
     * @param \DateTime|string $date
     * @return string
     */
    public function datetime(\DateTime|string $date): string
    {
        return FormatterHelper::date($date, $this->config->format_datetime);
    }

    /**
     * @param float|int|string $number
     * @return string
     */
    public function decimal(float|int|string $number): string
    {
        return FormatterHelper::number(
            $number,
            $this->config->format_number_decimals,
            $this->config->format_number_decimal_sep,
            $this->config->format_number_thousand_sep
        );
    }

    /**
     * @param float|int|string $number
     * @return string
     */
    public static function integer(float|int|string $number): string
    {
        return FormatterHelper::number($number);
    }
}
