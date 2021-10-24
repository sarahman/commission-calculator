<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Service\DataReader;

class DataFormatter
{
    /**
     * @param array $rawData
     * @return InputData
     */
    public function format(array $rawData): InputData
    {
        $inputData = new InputData();

        $inputData->setTransactionDate(isset($rawData[0]) ? $rawData[0] : null);
        $inputData->setUserIdentification(isset($rawData[1]) ? $rawData[1] : null);
        $inputData->setUserType(isset($rawData[2]) ? $rawData[2] : '');
        $inputData->setOperationType(isset($rawData[3]) ? $rawData[3] : '');
        $inputData->setOperationAmount(isset($rawData[4]) ? (float) ($rawData[4]) : 0.00);
        $inputData->setOperationCurrency(isset($rawData[5]) ? $rawData[5] : '');

        return $inputData;
    }
}
