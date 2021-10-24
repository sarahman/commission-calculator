<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\CommissionRule;

use Sarahman\CommissionTask\Service\DataReader\Transaction;

class DepositRule implements RuleContract
{
    public function applyRule(Transaction $transaction): Transaction
    {
        if ('deposit' === $transaction->getOperationType()) {
            $transaction->setCommission((0.03 / 100) * $transaction->getAmount());
        }

        return $transaction;
    }
}
