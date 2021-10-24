<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask;

use Sarahman\CommissionTask\CommissionRules\RuleContract;
use Sarahman\CommissionTask\Service\DataReader\DataReader;
use Sarahman\CommissionTask\Service\DataReader\Transaction;

class CommissionCalculator
{
    /**
     * @var DataReader
     */
    private $transactions;

    /**
     * @var array
     */
    private $rules = [];

    /**
     * @param DataReader $collection
     * @param array $rules
     */
    public function __construct(DataReader $collection, array $rules)
    {
        $this->transactions = $collection;
        $this->rules = $rules;
    }

    public function process(): array
    {
        $commissions = [];

        foreach ($this->transactions->getData() as $transaction) {
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
