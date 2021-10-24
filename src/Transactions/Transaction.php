<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Transactions;

use Sarahman\CommissionTask\Service\DataReader\InputData;

class Transaction
{
    private $userIdentification;
    private $userType;
    private $operationType;
    private $operationAmount;
    private $operationCurrency;
    private $transactionDate;
    private $commission = 0.00;

    public function __construct(InputData $obj)
    {
        $this->transactionDate = $obj->getTransactionDate();
        $this->userIdentification = $obj->getUserIdentification();
        $this->userType = $obj->getUserType();
        $this->operationType = $obj->getOperationType();
        $this->operationAmount = $obj->getOperationAmount();
        $this->operationCurrency = $obj->getOperationCurrency();
    }

    /**
     * @return bool
     */
    public function isCurrencyEuro(): bool
    {
        return 'EUR' === $this->operationCurrency;
    }

    /**
     * @return bool
     */
    public function isCurrencyJpy(): bool
    {
        return 'JPY' === $this->operationCurrency;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->operationAmount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->operationCurrency;
    }

    /**
     * @return string
     */
    public function getTransactionDate(): string
    {
        return $this->transactionDate;
    }

    /**
     * @return float
     */
    public function getCommission(): float
    {
        return $this->commission;
    }

    /**
     * @param float $commission
     */
    public function setCommission(float $commission)
    {
        if ($this->isCurrencyJpy()) {
            $commission = ceil($commission);
        }

        $this->commission = $commission;
    }

    /**
     * @return bool
     */
    public function isDeposit(): bool
    {
        return 'deposit' === $this->operationType;
    }

    /**
     * @return bool
     */
    public function isWithdraw(): bool
    {
        return 'withdraw' === $this->operationType;
    }

    /**
     * @return bool
     */
    public function isPrivateClient(): bool
    {
        return 'private' === $this->userType;
    }

    /**
     * @return bool
     */
    public function isBusinessClient(): bool
    {
        return 'business' === $this->userType;
    }

    /**
     * @return mixed
     */
    public function getUserIdentification()
    {
        return $this->userIdentification;
    }
}
