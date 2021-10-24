<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Service\DataReader;

class InputData
{
    private $transactionDate = '';
    private $userIdentification = '';
    private $userType = '';
    private $operationType = '';
    private $operationAmount = 0.00;
    private $operationCurrency = 'EUR';

    /**
     * @param string $transactionDate
     */
    public function setTransactionDate(string $transactionDate)
    {
        $this->transactionDate = $transactionDate;
    }

    public function getTransactionDate(): string
    {
        return $this->transactionDate;
    }

    /**
     * @param string $userIdentification
     */
    public function setUserIdentification(string $userIdentification)
    {
        $this->userIdentification = $userIdentification;
    }

    public function getUserIdentification(): string
    {
        return $this->userIdentification;
    }

    /**
     * @param string $userType
     */
    public function setUserType(string $userType)
    {
        $this->userType = $userType;
    }

    public function getUserType(): string
    {
        return $this->userType;
    }

    /**
     * @param string $operationType
     */
    public function setOperationType(string $operationType)
    {
        $this->operationType = strtolower($operationType);
    }

    public function getOperationType(): string
    {
        return $this->operationType;
    }

    /**
     * @param float $operationAmount
     */
    public function setOperationAmount(float $operationAmount)
    {
        $this->operationAmount = $operationAmount;
    }

    public function getOperationAmount(): float
    {
        return $this->operationAmount;
    }

    /**
     * @param string $operationCurrency
     */
    public function setOperationCurrency(string $operationCurrency)
    {
        $this->operationCurrency = strtoupper($operationCurrency);
    }

    public function getOperationCurrency(): string
    {
        return $this->operationCurrency;
    }
}
