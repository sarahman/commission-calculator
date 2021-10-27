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
            $transaction = (new Transaction())
                ->setTransactionDate($row[0])
                ->setUserIdentification($row[1])
                ->setUserType($row[2])
                ->setOperationType($row[3])
                ->setAmount((float)$row[4])
                ->setCurrency($row[5])
            ;

            yield $transaction;
        }
    }
}
