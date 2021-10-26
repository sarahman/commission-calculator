<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Tests;

use Generator;
use Sarahman\CommissionTask\Service\DataReader\DataReaderInterface;
use Sarahman\CommissionTask\Service\DataReader\Transaction;

class ArrayIterator implements DataReaderInterface
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getData(): Generator
    {
        foreach ($this->data as $row) {
            $transaction = new Transaction();

            $transaction->setTransactionDate($row[0]);
            $transaction->setUserIdentification($row[1]);
            $transaction->setUserType($row[2]);
            $transaction->setOperationType($row[3]);
            $transaction->setAmount($row[4]);
            $transaction->setCurrency($row[5]);
            $transaction->setCommission(0.00);

            yield $transaction;
        }
    }
}
