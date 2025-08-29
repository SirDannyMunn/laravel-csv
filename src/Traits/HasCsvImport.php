<?php

namespace SirDannyMunn\CsvImport\Traits;

trait HasCsvImport
{
    /**
     * Process CSV row before import (optional override in model)
     *
     * @param array $row
     * @return array
     */
    public function processCsvRow(array $row): array
    {
        return $row;
    }

    /**
     * After CSV import hook (optional override in model)
     *
     * @param mixed $instance
     * @return void
     */
    public function afterCsvImport($instance): void
    {
        // Optional hook for post-import processing
    }

    /**
     * Get CSV export data (optional override in model)
     *
     * @return array
     */
    public function toCsvArray(): array
    {
        return $this->toArray();
    }
}