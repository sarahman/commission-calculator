<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask;

use Sarahman\CommissionTask\CommissionRule\RuleContract;
use Sarahman\CommissionTask\Service\DataReader\DataReader;
use Sarahman\CommissionTask\Service\DataReader\Transaction;

class CommissionCalculator
{
    private $reader;
    private $rules;

    public function __construct(DataReader $reader, array $rules)
    {
        $this->reader = $reader;
        $this->rules = $rules;
    }

    public function process(): array
    {
        $commissions = [];

        foreach ($this->reader->getData() as $transaction) {
            /** @var Transaction $transaction */
            foreach ($this->rules as $rule) {
                /** @var RuleContract $rule */
                $rule->applyRule($transaction);
            }

            if ($transaction->isCurrencyJpy()) {
                $commissions[] = $transaction->getCommission();
            } else {
                $commissions[] = number_format($transaction->getCommission(), 2, '.', '');
            }
        }

        return $commissions;
    }
}
