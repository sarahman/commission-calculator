<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\Service\DataReader;

class CsvFormatter implements FormatterContract
{
    /**
     * @param array $rawData
     * @return InputData
     */
    public function format(array $rawData): InputData
    {
        $inputData = new InputData();

        $inputData->setTransactionDate(!empty($rawData[0]) ? $rawData[0] : null);
        $inputData->setUserIdentification(!empty($rawData[1]) ? $rawData[1] : null);
        $inputData->setUserType(!empty($rawData[2]) ? $rawData[2] : '');
        $inputData->setOperationType(!empty($rawData[3]) ? $rawData[3] : '');
        $inputData->setOperationAmount(!empty($rawData[4]) ? floatval($rawData[4]) : 0.00);
        $inputData->setOperationCurrency(!empty($rawData[5]) ? $rawData[5] : '');

        return $inputData;
    }
}
