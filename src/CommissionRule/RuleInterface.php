<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\CommissionRule;

use Sarahman\CommissionTask\Service\DataReader\Transaction;

interface RuleInterface
{
    public function supports(Transaction $transaction): bool;

    public function applyOn(Transaction $transaction): Transaction;
}
