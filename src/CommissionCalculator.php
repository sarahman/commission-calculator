<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask;

use Sarahman\CommissionTask\CommissionRule\RuleInterface;
use Sarahman\CommissionTask\Exception\CalculationException;
use Sarahman\CommissionTask\Service\DataReader\DataReaderInterface;
use Sarahman\CommissionTask\Service\DataReader\Transaction;
use Throwable;

class CommissionCalculator
{
    private DataReaderInterface $csvDataReader;

    /**
     * @var RuleInterface[]
     */
    private array $rules;

    public function __construct(DataReaderInterface $reader, array $rules)
    {
        $this->csvDataReader = $reader;
        $this->rules = $rules;
    }

    public function calculate(): array
    {
        $commissions = [];

        try {
            foreach ($this->csvDataReader->getData() as $transaction) {
                $commissions[] = $this->getTransactionalCommission($transaction);
            }
        } catch (Throwable $throwable) {
            throw new CalculationException('Commission calculation error occurred!', 500, $throwable);
        }

        return $commissions;
    }

    private function getTransactionalCommission(Transaction $transaction): string
    {
        foreach ($this->rules as $rule) {
            if ($rule->supports($transaction)) {
                $rule->applyOn($transaction);
            }
        }

        return $this->formatOutput($transaction);
    }

    private function formatOutput(Transaction $transaction): string
    {
        if ('JPY' === $transaction->getCurrency()) {
            return (string)ceil($transaction->getCommission());
        }

        return number_format($transaction->getCommission(), 2, '.', '');
    }
}
