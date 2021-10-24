<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Service\DataReader;

use Generator;

class CsvDataReader
{
    private $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function getData(): Generator
    {
        $handle = fopen($this->baseUrl, 'r');

        if (false === $handle) {
            return;
        }

        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            yield $this->format($data);
        }

        fclose($handle);
    }

    private function format(array $data)
    {
        return new Transaction($data);
    }
}
