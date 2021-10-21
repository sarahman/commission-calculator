<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\Service\DataReader;

interface FormatterContract
{
    /**
     * @param array $rawData
     * @return InputData
     */
    public function format(array $rawData): InputData;
}
