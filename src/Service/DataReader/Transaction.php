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

    public function __construct(array $data)
    {
        $this->transactionDate = isset($data[0]) ? $data[0] : '';
        $this->userIdentification = isset($data[1]) ? $data[1] : '';
        $this->userType = isset($data[2]) ? $data[2] : '';
        $this->operationType = isset($data[3]) ? $data[3] : '';
        $this->operationAmount = isset($data[4]) ? (float) ($data[4]) : 0.00;
        $this->operationCurrency = isset($data[5]) ? $data[5] : '';
        $this->commission = 0.00;
    }

    public function isCurrencyEuro(): bool
    {
        return 'EUR' === $this->operationCurrency;
    }

    public function isCurrencyJpy(): bool
    {
        return 'JPY' === $this->operationCurrency;
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

    public function getCommission(): float
    {
        return $this->commission;
    }

    public function setCommission(float $commission)
    {
        if ($this->isCurrencyJpy()) {
            $this->commission = ceil($commission);
        } else {
            $this->commission = $commission;
        }
    }

    public function isDeposit(): bool
    {
        return 'deposit' === $this->operationType;
    }

    public function isWithdraw(): bool
    {
        return 'withdraw' === $this->operationType;
    }

    public function isPrivateClient(): bool
    {
        return 'private' === $this->userType;
    }

    public function isBusinessClient(): bool
    {
        return 'business' === $this->userType;
    }

    public function getUserIdentification()
    {
        return $this->userIdentification;
    }
}
