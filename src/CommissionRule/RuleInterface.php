<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\CommissionRule;

use Sarahman\CommissionTask\Service\DataReader\Transaction;

interface RuleInterface
{
    public function applyRule(Transaction $transaction): Transaction;
}
