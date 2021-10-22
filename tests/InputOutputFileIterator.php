<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\Tests;

use Iterator;

final class InputOutputFileIterator implements Iterator
{
    private $inputFile;
    private $outputFile;
    private $key = 0;
    private $current;

    public function __construct(string $inputFile, string $outputFile)
    {
        $this->inputFile = fopen($inputFile, 'r');
        $this->outputFile = fopen($outputFile, 'r');
    }

    public function __destruct()
    {
        fclose($this->inputFile);
        fclose($this->outputFile);
    }

    public function rewind()
    {
        rewind($this->inputFile);
        rewind($this->outputFile);

        $this->current = array_merge((array) fgetcsv($this->inputFile), (array) fgetcsv($this->outputFile));

        $this->key = 0;
    }

    public function valid(): bool
    {
        return !(feof($this->inputFile) || feof($this->outputFile));
    }

    public function key(): int
    {
        return $this->key;
    }

    public function current(): array
    {
        return $this->current;
    }

    public function next()
    {
        $this->current = array_merge((array) fgetcsv($this->inputFile), (array) fgetcsv($this->outputFile));

        $this->key++;
    }
}
