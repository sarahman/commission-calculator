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
        if (!isset($data[0])) {
            $data[0] = '';
        }

        if (!isset($data[1])) {
            $data[1] = '';
        }

        if (!isset($data[2])) {
            $data[2] = '';
        }

        if (!isset($data[3])) {
            $data[3] = '';
        }

        if (isset($data[4])) {
            $data[4] = (float) ($data[4]);
        } else {
            $data[4] = 0.00;
        }

        if (!isset($data[5])) {
            $data[5] = '';
        }

        $transaction = new Transaction();

        $transaction->setTransactionDate($data[0]);
        $transaction->setUserIdentification($data[1]);
        $transaction->setUserType($data[2]);
        $transaction->setOperationType($data[3]);
        $transaction->setAmount($data[4]);
        $transaction->setCurrency($data[5]);
        $transaction->setCommission(0.00);

        return $transaction;
    }
}
