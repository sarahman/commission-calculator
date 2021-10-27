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

    public function __construct()
    {
        $this->commission = 0.00;
    }

    public function getTransactionDate(): string
    {
        return $this->transactionDate;
    }

    public function setTransactionDate(string $date): self
    {
        $this->transactionDate = $date;

        return $this;
    }

    public function getUserIdentification(): string
    {
        return $this->userIdentification;
    }

    public function setUserIdentification(string $userIdentification): self
    {
        $this->userIdentification = $userIdentification;

        return $this;
    }

    public function getUserType(): string
    {
        return $this->userType;
    }

    public function setUserType(string $userType): self
    {
        $this->userType = $userType;

        return $this;
    }

    public function getOperationType(): string
    {
        return $this->operationType;
    }

    public function setOperationType(string $operationType): self
    {
        $this->operationType = $operationType;

        return $this;
    }

    public function getAmount(): float
    {
        return $this->operationAmount;
    }

    public function setAmount(float $operationAmount): self
    {
        $this->operationAmount = $operationAmount;

        return $this;
    }

    public function getCurrency(): string
    {
        return $this->operationCurrency;
    }

    public function setCurrency(string $operationCurrency): self
    {
        $this->operationCurrency = $operationCurrency;

        return $this;
    }

    public function getCommission(): float
    {
        return $this->commission;
    }

    public function setCommission(float $commission): self
    {
        $this->commission = $commission;

        return $this;
    }
}
