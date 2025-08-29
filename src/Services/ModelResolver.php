<?php

namespace SirDannyMunn\CsvImport\Services;

use SirDannyMunn\CsvImport\Exceptions\InvalidModelException;
use SirDannyMunn\CsvImport\Contracts\CsvImportable;

class ModelResolver
{
    protected array $allowedModels;

    public function __construct()
    {
        $this->allowedModels = config('csv-importer.allowed_models', []);
    }

    /**
     * Resolve model string to class instance
     * 
     * @param string $model
     * @return string
     * @throws InvalidModelException
     */
    public function resolve(string $model): string
    {
        if (!isset($this->allowedModels[$model])) {
            throw InvalidModelException::notFound($model);
        }

        $modelClass = $this->allowedModels[$model]['class'];

        if (!class_exists($modelClass)) {
            throw InvalidModelException::invalidClass($modelClass);
        }

        if (!in_array(CsvImportable::class, class_implements($modelClass))) {
            throw InvalidModelException::missingInterface($modelClass);
        }

        return $modelClass;
    }

    /**
     * Get model configuration
     * 
     * @param string $model
     * @return array
     */
    public function getConfig(string $model): array
    {
        return $this->allowedModels[$model] ?? [];
    }

    /**
     * Get all available models
     * 
     * @return array
     */
    public function getAvailableModels(): array
    {
        return array_keys($this->allowedModels);
    }
    
    /**
     * Check if model exists and is allowed
     * 
     * @param string $model
     * @return bool
     */
    public function isAllowed(string $model): bool
    {
        return isset($this->allowedModels[$model]);
    }
    
    /**
     * Get chunk size for model
     * 
     * @param string $model
     * @return int
     */
    public function getChunkSize(string $model): int
    {
        return $this->allowedModels[$model]['chunk_size'] 
            ?? config('csv-importer.defaults.chunk_size', 100);
    }
    
    /**
     * Get timeout for model
     * 
     * @param string $model
     * @return int
     */
    public function getTimeout(string $model): int
    {
        return $this->allowedModels[$model]['timeout'] 
            ?? config('csv-importer.queue.timeout', 600);
    }
    
    /**
     * Get permissions for model
     * 
     * @param string $model
     * @return array
     */
    public function getPermissions(string $model): array
    {
        return $this->allowedModels[$model]['permissions'] ?? [
            'import' => "nexopos.import.{$model}",
            'export' => "nexopos.export.{$model}"
        ];
    }
}