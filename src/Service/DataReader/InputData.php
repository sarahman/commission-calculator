<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Service\DataReader;

class InputData
{
    /**
     * @var string
     */
    private $transactionDate;

    /**
     * @var string
     */
    private $userIdentification;

    /**
     * @var string
     */
    private $userType;

    /**
     * @var string
     */
    private $operationType;

    /**
     * @var float
     */
    private $operationAmount;

    /**
     * @var string
     */
    private $operationCurrency;

    public function __construct()
    {
        $this->transactionDate = '';
        $this->userIdentification = '';
        $this->userType = '';
        $this->operationType = '';
        $this->operationAmount = 0.00;
        $this->operationCurrency = 'EUR';
    }

    public function setTransactionDate(string $transactionDate)
    {
        $this->transactionDate = $transactionDate;
    }

    public function getTransactionDate(): string
    {
        return $this->transactionDate;
    }

    public function setUserIdentification(string $userIdentification)
    {
        $this->userIdentification = $userIdentification;
    }

    public function getUserIdentification(): string
    {
        return $this->userIdentification;
    }

    public function setUserType(string $userType)
    {
        $this->userType = $userType;
    }

    public function getUserType(): string
    {
        return $this->userType;
    }

    public function setOperationType(string $operationType)
    {
        $this->operationType = strtolower($operationType);
    }

    public function getOperationType(): string
    {
        return $this->operationType;
    }

    public function setOperationAmount(float $operationAmount)
    {
        $this->operationAmount = $operationAmount;
    }

    public function getOperationAmount(): float
    {
        return $this->operationAmount;
    }

    public function setOperationCurrency(string $operationCurrency)
    {
        $this->operationCurrency = strtoupper($operationCurrency);
    }

    public function getOperationCurrency(): string
    {
        return $this->operationCurrency;
    }
}
