<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Service\DataReader;

use Generator;

interface DataReaderInterface
{
    public function getData(): Generator;
}
