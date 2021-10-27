<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\CommissionRule;

use Sarahman\CommissionTask\Service\DataReader\Transaction;

class DepositRule implements RuleInterface
{
    private float $commissionPercentage;

    public function __construct(float $commissionPercentage)
    {
        $this->commissionPercentage = $commissionPercentage;
    }

    public function supports(Transaction $transaction): bool
    {
        return 'deposit' === $transaction->getOperationType();
    }

    public function applyOn(Transaction $transaction): Transaction
    {
        $transaction->setCommission($this->commissionPercentage / 100 * $transaction->getAmount());

        return $transaction;
    }
}
