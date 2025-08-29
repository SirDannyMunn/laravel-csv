<?php

namespace SirDannyMunn\CsvImport\Exceptions;

use Exception;

class CsvImportException extends Exception
{
    protected array $errors = [];
    protected int $rowNumber = 0;
    
    /**
     * Create a new CSV import exception.
     *
     * @param string $message
     * @param array $errors
     * @param int $rowNumber
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $message = "", 
        array $errors = [], 
        int $rowNumber = 0,
        int $code = 0, 
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
        $this->rowNumber = $rowNumber;
    }
    
    /**
     * Get validation errors
     * 
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    /**
     * Get the row number where the error occurred
     * 
     * @return int
     */
    public function getRowNumber(): int
    {
        return $this->rowNumber;
    }
    
    /**
     * Create exception for validation failure
     * 
     * @param array $errors
     * @param int $rowNumber
     * @return static
     */
    public static function validationFailed(array $errors, int $rowNumber): static
    {
        $message = "Validation failed at row {$rowNumber}";
        return new static($message, $errors, $rowNumber);
    }
    
    /**
     * Create exception for file not found
     * 
     * @param string $path
     * @return static
     */
    public static function fileNotFound(string $path): static
    {
        return new static("CSV file not found: {$path}");
    }
    
    /**
     * Create exception for invalid file format
     * 
     * @param string $extension
     * @return static
     */
    public static function invalidFormat(string $extension): static
    {
        return new static("Invalid file format: {$extension}. Only CSV and TXT files are allowed.");
    }
}