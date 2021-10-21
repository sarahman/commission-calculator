<?php

declare(strict_types=1);

namespace Paysera\CommissionTask;

use Paysera\CommissionTask\CommissionRules\RuleContract;
use Paysera\CommissionTask\Transactions\Collection;
use Paysera\CommissionTask\Transactions\Transaction;

class CalculatorManager
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
     * @return $this
     */
    public function addTransactions(Collection $collection)
    {
        $this->transactions = $collection;

        return $this;
    }

    /**
     * @param RuleContract $rule
     * @return $this
     */
    public function addRule(RuleContract $rule)
    {
        $this->rules[] = $rule;

        return $this;
    }

    /**
     * @return $this
     */
    public function applyAllRules()
    {
        if (count($this->rules)) {
            $this->transactions->each(function ($eachTransaction) {
                foreach ($this->rules as $rule) {
                    /** @var RuleContract $rule */
                    $rule->applyRule($eachTransaction);
                }
            });
        }

        return $this;
    }

    public function getCommissions()
    {
        $commissions = [];

        $this->transactions->each(function ($eachTransaction) use (&$commissions) {
            /** @var Transaction $eachTransaction */
            $commissions[] = number_format($eachTransaction->getCommission(), 2, '.', '');
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
