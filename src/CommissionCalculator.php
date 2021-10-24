<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask;

use Sarahman\CommissionTask\CommissionRules\RuleContract;
use Sarahman\CommissionTask\Transactions\Collection;
use Sarahman\CommissionTask\Transactions\Transaction;

class CommissionCalculator
{
    /**
     * @var Collection
     */
    private $transactions;

    /**
     * @var array
     */
    private $rules = [];

    /**
     * @param Collection $collection
     * @param array $rules
     */
    public function __construct(Collection $collection, array $rules)
    {
        $this->transactions = $collection;
        $this->rules = $rules;
    }

    public function process(): array
    {
        $commissions = [];

        $this->transactions->each(function ($transaction) use (&$commissions) {
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
        });

        return $commissions;
    }

    /**
     * @return array
     */
    public function getAllTransactions(): array
    {
        return $this->transactions->all();
    }
}
