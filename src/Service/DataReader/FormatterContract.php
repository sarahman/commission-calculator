<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Service\DataReader;

interface FormatterContract
{
    /**
     * @param array $rawData
     * @return InputData
     */
    public function format(array $rawData): InputData;
}
