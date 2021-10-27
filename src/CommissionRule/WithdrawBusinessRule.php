<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\CommissionRule;

use Sarahman\CommissionTask\Service\DataReader\Transaction;

class WithdrawBusinessRule implements RuleInterface
{
    private float $commissionPercentage;

    public function __construct(float $commissionPercentage)
    {
        $this->commissionPercentage = $commissionPercentage;
    }

    public function supports(Transaction $transaction): bool
    {
        return
            'withdraw' === $transaction->getOperationType()
            && 'business' === $transaction->getUserType()
        ;
    }

    public function applyOn(Transaction $transaction): Transaction
    {
        $transaction->setCommission($this->commissionPercentage / 100 * $transaction->getAmount());

        return $transaction;
    }
}
