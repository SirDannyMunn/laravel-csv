<?php

namespace Vitorccs\LaravelCsv\Handlers\Readers;

interface Handler
{
    /**
     * @return int
     */
    public function count(): int;

    /**
     * @return array
     */
    public function getAll(): array;

    /**
     * @param callable(array,int):void $callable
     * @param int $size
     * @param int|null $maxRecords
     * @return void
     */
    public function getChunk(callable $callable,
                             int      $size,
                             ?int     $maxRecords = null): void;

    /**
     * @return resource
     */
    public function getResource();
}
