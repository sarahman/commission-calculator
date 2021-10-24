<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\CommissionRules;

use Sarahman\CommissionTask\Service\DataReader\Transaction;

interface RuleContract
{
    public function applyRule(Transaction $transaction): Transaction;
}
