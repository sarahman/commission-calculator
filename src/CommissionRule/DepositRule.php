<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\CommissionRule;

use Sarahman\CommissionTask\Service\DataReader\Transaction;

class DepositRule implements RuleContract
{
    public function applyRule(Transaction $transaction): Transaction
    {
        if ($transaction->isDeposit()) {
            $transaction->setCommission((0.03 / 100) * $transaction->getAmount());
        }

        return $transaction;
    }
}
