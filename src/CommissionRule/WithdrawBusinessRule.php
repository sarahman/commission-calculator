<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\CommissionRule;

use Sarahman\CommissionTask\Service\DataReader\Transaction;

class WithdrawBusinessRule implements RuleContract
{
    private float $commissionPercentage;

    public function __construct(float $commissionPercentage)
    {
        $this->commissionPercentage = $commissionPercentage;
    }

    public function applyRule(Transaction $transaction): Transaction
    {
        if ('withdraw' === $transaction->getOperationType() && 'business' === $transaction->getUserType()) {
            $transaction->setCommission($this->commissionPercentage / 100 * $transaction->getAmount());
        }

        return $transaction;
    }
}
