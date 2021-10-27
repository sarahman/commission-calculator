<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Service\DataReader;

use Generator;

class CsvDataReader implements DataReaderInterface
{
    private string $filepath;

    public function __construct(string $filepath)
    {
        $this->filepath = $filepath;
    }

    public function getData(): Generator
    {
        $handle = fopen($this->filepath, 'r');

        if (false === $handle) {
            return;
        }

        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            yield $this->transform($data);
        }

        fclose($handle);
    }

    private function transform(array $data): Transaction
    {
        return (new Transaction())
            ->setTransactionDate($data[0])
            ->setUserIdentification($data[1])
            ->setUserType($data[2])
            ->setOperationType($data[3])
            ->setAmount((float)$data[4])
            ->setCurrency($data[5])
        ;
    }
}
