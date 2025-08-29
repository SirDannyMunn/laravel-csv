<?php

namespace SirDannyMunn\CsvImport\Exceptions;

use Exception;

class InvalidModelException extends Exception
{
    /**
     * Create a new invalid model exception.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * Create exception for non-existent model
     * 
     * @param string $model
     * @return static
     */
    public static function notFound(string $model): static
    {
        return new static("Model '{$model}' is not configured for CSV import/export");
    }
    
    /**
     * Create exception for model without interface
     * 
     * @param string $model
     * @return static
     */
    public static function missingInterface(string $model): static
    {
        return new static("Model '{$model}' must implement CsvImportable interface");
    }
    
    /**
     * Create exception for invalid model class
     * 
     * @param string $class
     * @return static
     */
    public static function invalidClass(string $class): static
    {
        return new static("Model class '{$class}' does not exist");
    }
}