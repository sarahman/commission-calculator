<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Service\DataReader;

class Transaction
{
    private string $userIdentification;
    private string $userType;
    private string $operationType;
    private float $operationAmount;
    private string $operationCurrency;
    private string $transactionDate;
    private float $commission;

    public function __construct(
        string $date,
        string $userIdentification,
        string $userType,
        string $operationType,
        float $operationAmount,
        string $operationCurrency
    ) {
        $this->transactionDate = $date;
        $this->userIdentification = $userIdentification;
        $this->userType = $userType;
        $this->operationType = $operationType;
        $this->operationAmount = $operationAmount;
        $this->operationCurrency = $operationCurrency;
        $this->commission = 0.00;
    }

    public function getAmount(): float
    {
        return $this->operationAmount;
    }

    public function getCurrency(): string
    {
        return $this->operationCurrency;
    }

    public function getTransactionDate(): string
    {
        return $this->transactionDate;
    }

    public function getOperationType(): string
    {
        return $this->operationType;
    }

    public function getCommission(): float
    {
        return $this->commission;
    }

    public function setCommission(float $commission)
    {
        $this->commission = $commission;
    }

    public function getUserIdentification()
    {
        return $this->userIdentification;
    }

    public function getUserType(): string
    {
        return $this->userType;
    }
}
