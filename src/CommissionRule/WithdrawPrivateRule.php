<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\CommissionRule;

use DateTime;
use Sarahman\CommissionTask\Service\DataReader\Transaction;

class WithdrawPrivateRule implements RuleInterface
{
    private float $commissionPercentage;
    private string $baseCurrency;
    private array $exchangeRates;
    private int $weeklyFreeTransactionCount;
    private float $weeklyChargeFreeAmount;
    private array $history;

    public function __construct($commissionPercentage, string $baseCurrency, array $exchangeRates, float $weeklyChargeFreeAmount, int $weeklyFreeTransactionCount)
    {
        $this->commissionPercentage = $commissionPercentage;
        $this->baseCurrency = $baseCurrency;
        $this->exchangeRates = $exchangeRates;
        $this->weeklyFreeTransactionCount = $weeklyFreeTransactionCount;
        $this->weeklyChargeFreeAmount = $weeklyChargeFreeAmount;
        $this->history = [];
    }

    public function applyRule(Transaction $transaction): Transaction
    {
        if ('withdraw' === $transaction->getOperationType() && 'private' === $transaction->getUserType()) {
            $index = sprintf(
                '%s:%s',
                $transaction->getUserIdentification(),
                $this->getWeekCount($transaction->getTransactionDate())
            );

            if (isset($this->history[$index])) {
                $weeklyHistory = $this->history[$index];
            } else {
                $weeklyHistory = ['totalAmount' => 0.00, 'transactionCount' => 0];
            }

            if (
                $this->baseCurrency === $transaction->getCurrency()
                || !isset($this->exchangeRates[$transaction->getCurrency()])
            ) {
                $rate = 1.0;
            } else {
                $rate = $this->exchangeRates[$transaction->getCurrency()];
            }

            $exchangedAmount = $transaction->getAmount() / $rate;

            if (
                $weeklyHistory['transactionCount'] >= $this->weeklyFreeTransactionCount
                || $weeklyHistory['totalAmount'] >= $this->weeklyChargeFreeAmount
            ) {
                $chargeableAmount = $exchangedAmount;
            } elseif (
                $weeklyHistory['transactionCount'] < $this->weeklyFreeTransactionCount
                && $weeklyHistory['totalAmount'] + $exchangedAmount <= $this->weeklyChargeFreeAmount
            ) {
                $chargeableAmount = 0.00;
            } else {
                $chargeableAmount = abs(($weeklyHistory['totalAmount'] + $exchangedAmount) - $this->weeklyChargeFreeAmount);
            }

            $transaction->setCommission(($this->commissionPercentage / 100) * $chargeableAmount * $rate);
            $this->updateHistory($index, $weeklyHistory, $exchangedAmount);
        }

        return $transaction;
    }

    private function updateHistory(string $index, array $weeklyHistory, float $amount): void
    {
        $weeklyHistory['totalAmount'] += $amount;
        $weeklyHistory['transactionCount']++;

        $this->history[$index] = $weeklyHistory;
    }

    private function getWeekCount(string $date): string
    {
        $startDate = new DateTime('1970-01-05');
        $endDate = new DateTime($date);
        $totalDays = (int) $endDate->diff($startDate)->format('%a');

        return 'W#' . (string) (ceil($totalDays / 7) + ($totalDays % 7 ? 0 : 1));
    }
}
