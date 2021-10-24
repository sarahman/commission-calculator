<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask;

use Sarahman\CommissionTask\CommissionRule\RuleContract;
use Sarahman\CommissionTask\Service\DataReader\CsvDataReader;
use Sarahman\CommissionTask\Service\DataReader\Transaction;

class CommissionCalculator
{
    private $reader;
    private $rules;

    public function __construct(CsvDataReader $reader, array $rules)
    {
        $this->reader = $reader;
        $this->rules = $rules;
    }

    public function calculate(): array
    {
        $commissions = [];

        foreach ($this->reader->getData() as $transaction) {
            $commissions[] = $this->getTransactionalCommission($transaction);
        }

        return $commissions;
    }

    private function getTransactionalCommission(Transaction $transaction): string
    {
        foreach ($this->rules as $rule) {
            /** @var RuleContract $rule */
            $rule->applyRule($transaction);
        }

        return $this->formatOutput($transaction);
    }

    private function formatOutput(Transaction $transaction): string
    {
        if ('JPY' === $transaction->getCurrency()) {
            return (string) ceil($transaction->getCommission());
        }

        return number_format($transaction->getCommission(), 2, '.', '');
    }
}
