<?php

namespace SirDannyMunn\CsvImport\Contracts;

interface CsvImportable
{
    /**
     * Get CSV fields configuration
     *
     * @return array
     */
    public function getCsvFields(): array;

    /**
     * Get CSV validation rules
     *
     * @param array $mapping Column mapping configuration
     * @return array
     */
    public function getCsvValidationRules(array $mapping = []): array;
}