<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\CommissionRule;

use Sarahman\CommissionTask\Service\DataReader\Transaction;

class WithdrawBusinessRule implements RuleContract
{
    /**
     * @param Transaction $transaction
     * @return Transaction
     */
    public function applyRule(Transaction $transaction): Transaction
    {
        if ($transaction->isWithdraw() && $transaction->isBusinessClient()) {
            $transaction->setCommission((0.5 / 100) * $transaction->getAmount());
        }

        return $transaction;
    }
}
