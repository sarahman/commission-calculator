<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Service\DataReader;

class Transaction
{
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

    /**
     * @var string
     */
    private $transactionDate;

    /**
     * @var float
     */
    private $commission;

    public function __construct(InputData $obj)
    {
        $this->transactionDate = $obj->getTransactionDate();
        $this->userIdentification = $obj->getUserIdentification();
        $this->userType = $obj->getUserType();
        $this->operationType = $obj->getOperationType();
        $this->operationAmount = $obj->getOperationAmount();
        $this->operationCurrency = $obj->getOperationCurrency();
        $this->commission = 0.00;
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
            $this->commission = ceil($commission);
        } else {
            $this->commission = $commission;
        }
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
     * @return string
     */
    public function getUserIdentification()
    {
        return $this->userIdentification;
    }
}
