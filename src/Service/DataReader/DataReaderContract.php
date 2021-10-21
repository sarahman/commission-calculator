<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\Service\DataReader;

interface DataReaderContract
{
    /**
     * @param FormatterContract $formatter
     * @return DataReaderContract
     */
    public function setFormatter(FormatterContract $formatter): DataReaderContract;

    /**
     * @return DataReaderContract
     */
    public function parseData(): DataReaderContract;

    /**
     * @return array
     */
    public function getData(): array;
}
