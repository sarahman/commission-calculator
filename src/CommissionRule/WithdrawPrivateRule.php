<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\CommissionRule;

use DateTime;
use Sarahman\CommissionTask\Service\DataReader\Transaction;
use Sarahman\CommissionTask\Service\ExchangeRate\Client;

class WithdrawPrivateRule implements RuleContract
{
    private $exchangeClient;

    /**
     * @var int
     */
    private $weeklyFreeTransactionCount;

    /**
     * @var float
     */
    private $weeklyChargeFreeAmount;

    /**
     * @var float
     */
    private $commissionFee;

    /**
     * @var array
     */
    private $history;

    public function __construct(Client $exchangeClient)
    {
        $this->exchangeClient = $exchangeClient;
        $this->weeklyFreeTransactionCount = 3;
        $this->weeklyChargeFreeAmount = 1000;
        $this->commissionFee = 0.3;
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

            if ('EUR' === $transaction->getCurrency()) {
                $rate = 1.0;
            } else {
                $rate = $this->exchangeClient->getRate($transaction->getCurrency());
            }

            $euroAmount = $transaction->getAmount() / $rate;

            if (
                $weeklyHistory['transactionCount'] >= $this->weeklyFreeTransactionCount
                || $weeklyHistory['totalAmount'] >= $this->weeklyChargeFreeAmount
            ) {
                $chargeableAmount = $euroAmount;
            } elseif (
                $weeklyHistory['transactionCount'] < $this->weeklyFreeTransactionCount
                && $weeklyHistory['totalAmount'] + $euroAmount <= $this->weeklyChargeFreeAmount
            ) {
                $chargeableAmount = 0.00;
            } else {
                $chargeableAmount = abs(($weeklyHistory['totalAmount'] + $euroAmount) - $this->weeklyChargeFreeAmount);
            }

            $transaction->setCommission(($this->commissionFee / 100) * $chargeableAmount * $rate);
            $this->updateHistory($index, $weeklyHistory, $euroAmount);
        }

        return $transaction;
    }

    private function updateHistory(string $index, array $weeklyHistory, float $amount): bool
    {
        $weeklyHistory['totalAmount'] += $amount;
        $weeklyHistory['transactionCount']++;

        $this->history[$index] = $weeklyHistory;

        return true;
    }

    private function getWeekCount(string $date): string
    {
        $startDate = new DateTime('1970-01-05');
        $endDate = new DateTime($date);
        $totalDays = (int) $endDate->diff($startDate)->format('%a');

        return 'W#' . (string) (ceil($totalDays / 7) + ($totalDays % 7 ? 0 : 1));
    }
}
