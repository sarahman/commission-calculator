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
            yield new Transaction($row[0], $row[1], $row[2], $row[3], $row[4], $row[5]);
        }
    }
}
