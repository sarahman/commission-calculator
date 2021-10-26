<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\CommissionRule;

use Sarahman\CommissionTask\Service\DataReader\Transaction;

class DepositRule implements RuleContract
{
    private float $commissionPercentage;

    public function __construct(float $commissionPercentage)
    {
        $this->commissionPercentage = $commissionPercentage;
    }

    public function applyRule(Transaction $transaction): Transaction
    {
        if ('deposit' === $transaction->getOperationType()) {
            $transaction->setCommission($this->commissionPercentage / 100 * $transaction->getAmount());
        }

        return $transaction;
    }
}
