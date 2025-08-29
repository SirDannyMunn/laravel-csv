<?php

namespace SirDannyMunn\CsvImport\Services;

use League\Csv\Reader;
use League\Csv\Writer;
use League\Csv\CharsetConverter;
use Illuminate\Support\Facades\Storage;
use SirDannyMunn\CsvImport\Exceptions\CsvImportException;

class CsvProcessor
{
    protected string $defaultDisk;
    protected string $defaultPath;
    protected array $config;
    
    public function __construct()
    {
        $this->defaultDisk = config('csv-importer.storage.disk', 'local');
        $this->defaultPath = config('csv-importer.storage.path', 'csv-imports');
        $this->config = config('csv-importer.defaults', []);
    }
    
    /**
     * Read CSV file and return reader instance
     * 
     * @param string $path
     * @param array $options
     * @return Reader
     * @throws CsvImportException
     */
    public function read(string $path, array $options = []): Reader
    {
        if (!Storage::disk($this->defaultDisk)->exists($path)) {
            throw CsvImportException::fileNotFound($path);
        }
        
        $content = Storage::disk($this->defaultDisk)->get($path);
        $reader = Reader::createFromString($content);
        
        // Set CSV controls
        $delimiter = $options['delimiter'] ?? $this->config['delimiter'] ?? ',';
        $enclosure = $options['enclosure'] ?? $this->config['enclosure'] ?? '"';
        $escape = $options['escape'] ?? $this->config['escape'] ?? '\\';
        
        $reader->setDelimiter($delimiter);
        $reader->setEnclosure($enclosure);
        $reader->setEscape($escape);
        
        // Handle encoding
        $encoding = $this->detectEncoding($content);
        if ($encoding !== 'UTF-8') {
            $encoder = (new CharsetConverter())
                ->inputEncoding($encoding)
                ->outputEncoding('UTF-8');
            $reader->addFormatter($encoder);
        }
        
        // Set header offset if specified
        if (isset($options['header_offset'])) {
            $reader->setHeaderOffset($options['header_offset']);
        } else {
            $reader->setHeaderOffset(0); // Default to first row as header
        }
        
        return $reader;
    }
    
    /**
     * Write data to CSV file
     * 
     * @param string $path
     * @param array $data
     * @param array $headers
     * @param array $options
     * @return string Full path to the written file
     */
    public function write(string $path, array $data, array $headers = [], array $options = []): string
    {
        $writer = Writer::createFromString();
        
        // Set CSV controls
        $delimiter = $options['delimiter'] ?? $this->config['delimiter'] ?? ',';
        $enclosure = $options['enclosure'] ?? $this->config['enclosure'] ?? '"';
        $escape = $options['escape'] ?? $this->config['escape'] ?? '\\';
        
        $writer->setDelimiter($delimiter);
        $writer->setEnclosure($enclosure);
        $writer->setEscape($escape);
        
        // Add headers if provided
        if (!empty($headers)) {
            $writer->insertOne($headers);
        }
        
        // Insert data
        $writer->insertAll($data);
        
        // Save to disk
        $fullPath = $this->defaultPath . '/' . $path;
        Storage::disk($this->defaultDisk)->put($fullPath, $writer->toString());
        
        return $fullPath;
    }
    
    /**
     * Get headers from CSV file
     * 
     * @param string $path
     * @param array $options
     * @return array
     */
    public function getHeaders(string $path, array $options = []): array
    {
        $reader = $this->read($path, $options);
        return $reader->getHeader();
    }
    
    /**
     * Get sample rows from CSV file
     * 
     * @param string $path
     * @param int $limit
     * @param array $options
     * @return array
     */
    public function getSampleRows(string $path, int $limit = 5, array $options = []): array
    {
        $reader = $this->read($path, $options);
        $rows = [];
        $count = 0;
        
        foreach ($reader as $row) {
            if ($count >= $limit) {
                break;
            }
            $rows[] = $row;
            $count++;
        }
        
        return $rows;
    }
    
    /**
     * Count total rows in CSV file
     * 
     * @param string $path
     * @param array $options
     * @return int
     */
    public function countRows(string $path, array $options = []): int
    {
        $reader = $this->read($path, $options);
        return count($reader);
    }
    
    /**
     * Process CSV file in chunks
     * 
     * @param string $path
     * @param callable $callback
     * @param int $chunkSize
     * @param array $options
     * @return void
     */
    public function processInChunks(string $path, callable $callback, int $chunkSize = 100, array $options = []): void
    {
        $reader = $this->read($path, $options);
        $chunk = [];
        $chunkNumber = 0;
        
        foreach ($reader as $offset => $row) {
            $chunk[] = $row;
            
            if (count($chunk) >= $chunkSize) {
                $callback($chunk, $chunkNumber, $offset);
                $chunk = [];
                $chunkNumber++;
            }
        }
        
        // Process remaining rows
        if (!empty($chunk)) {
            $callback($chunk, $chunkNumber, count($reader));
        }
    }
    
    /**
     * Detect file encoding
     * 
     * @param string $content
     * @return string
     */
    protected function detectEncoding(string $content): string
    {
        $encodings = ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'ASCII'];
        
        foreach ($encodings as $encoding) {
            if (mb_check_encoding($content, $encoding)) {
                return $encoding;
            }
        }
        
        // Default to UTF-8 if no encoding detected
        return 'UTF-8';
    }
    
    /**
     * Validate CSV file structure
     * 
     * @param string $path
     * @param array $requiredHeaders
     * @return bool
     * @throws CsvImportException
     */
    public function validateStructure(string $path, array $requiredHeaders = []): bool
    {
        $headers = $this->getHeaders($path);
        
        if (empty($headers)) {
            throw new CsvImportException('CSV file has no headers');
        }
        
        if (!empty($requiredHeaders)) {
            $missingHeaders = array_diff($requiredHeaders, $headers);
            if (!empty($missingHeaders)) {
                throw new CsvImportException(
                    'Missing required headers: ' . implode(', ', $missingHeaders)
                );
            }
        }
        
        return true;
    }
    
    /**
     * Clean and store uploaded file
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @return string Path to stored file
     */
    public function storeUploadedFile($file, string $directory = 'temp'): string
    {
        $filename = uniqid('csv_') . '_' . $file->getClientOriginalName();
        $path = $this->defaultPath . '/' . $directory . '/' . $filename;
        
        Storage::disk($this->defaultDisk)->put($path, $file->get());
        
        return $path;
    }
    
    /**
     * Delete CSV file
     * 
     * @param string $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        return Storage::disk($this->defaultDisk)->delete($path);
    }
}