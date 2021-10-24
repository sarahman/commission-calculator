<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\CommissionRules;

use DateTime;
use Sarahman\CommissionTask\Service\ExchangeRate\ClientContract;
use Sarahman\CommissionTask\Service\History\WeeklyHistory;
use Sarahman\CommissionTask\Transactions\Transaction;

class WithdrawPrivateRule implements RuleContract
{
    private $historyManager;
    private $exchangeClient;
    private $weeklyFreeTransactionCount = 3;
    private $weeklyChargeFreeAmount = 1000;
    private $commissionFee = 0.3;

    public function __construct(ClientContract $exchangeClient, WeeklyHistory $historyManager = null)
    {
        $this->exchangeClient = $exchangeClient;

        if (is_null($historyManager)) {
            $historyManager = new WeeklyHistory();
        }

        $this->historyManager = $historyManager;
    }

    /**
     * @param Transaction $transaction
     * @return Transaction
     */
    public function applyRule(Transaction $transaction): Transaction
    {
        if ($transaction->isWithdraw() && $transaction->isPrivateClient()) {
            $index = sprintf("%s:%s", $transaction->getUserIdentification(), $this->getWeekCount($transaction->getTransactionDate()));
            $weeklyHistory = $this->historyManager->getData($index);

            if (empty($weeklyHistory)) {
                $weeklyHistory = ['totalAmount' => 0.00, 'transactionCount' => 0];
            }

            if ($transaction->isCurrencyEuro()) {
                $rate = 1.0;
            } else {
                $rate = $this->exchangeClient->getRate($transaction->getCurrency());
            }

            $euroAmount = $transaction->getAmount() / $rate;

            if ($weeklyHistory['transactionCount'] >= $this->weeklyFreeTransactionCount || $weeklyHistory['totalAmount'] >= $this->weeklyChargeFreeAmount) {
                $chargeableAmount = $euroAmount;
            } elseif ($weeklyHistory['transactionCount'] < $this->weeklyFreeTransactionCount && $weeklyHistory['totalAmount'] + $euroAmount <= $this->weeklyChargeFreeAmount) {
                $chargeableAmount = 0.00;
            } else {
                $chargeableAmount = abs(($weeklyHistory['totalAmount'] + $euroAmount) - $this->weeklyChargeFreeAmount);
            }

            $transaction->setCommission(($this->commissionFee / 100) * $chargeableAmount * $rate);
            $this->updateHistory($index, $weeklyHistory, $euroAmount);
        }

        return $transaction;
    }

    /**
     * @param string $index
     * @param array $weeklyHistory
     * @param float $amount
     * @return bool
     */
    protected function updateHistory(string $index, array $weeklyHistory, float $amount): bool
    {
        $weeklyHistory['totalAmount'] += $amount;
        ++$weeklyHistory['transactionCount'];

        return $this->historyManager->saveData($index, $weeklyHistory);
    }

    private function getWeekCount(string $date)
    {
        $startDate = new DateTime('1970-01-05');
        $endDate = new DateTime($date);
        $totalDays = (int) $endDate->diff($startDate)->format("%a");

        return 'W#' . (string) (ceil($totalDays / 7) + ($totalDays % 7 ? 0 : 1));
    }
}
