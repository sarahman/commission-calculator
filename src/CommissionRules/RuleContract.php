<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\CommissionRules;

use Paysera\CommissionTask\Transactions\Transaction;

interface RuleContract
{
    public function applyRule(Transaction $transaction): Transaction;
}
