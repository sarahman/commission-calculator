<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\CommissionRules;

use DateTime;
use Paysera\CommissionTask\Service\ExchangeRate\RateContract;
use Paysera\CommissionTask\Service\History\WeeklyHistory;
use Paysera\CommissionTask\Transactions\Transaction;

class WithdrawPrivateRule implements RuleContract
{
    private $historyManager;
    private $exchangeRateService;
    private $weeklyFreeTransactionCount = 3;
    private $weeklyChargeFreeAmount = 1000;
    private $commissionFee = 0.3;

    public function __construct(RateContract $exchangeRateService, WeeklyHistory $historyManager = null)
    {
        $this->exchangeRateService = $exchangeRateService;

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
                $rate = $this->exchangeRateService->getRate($transaction->getCurrency());
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
        $startDate = new DateTime('1970-01-04');
        $endDate = new DateTime($date);

        $totalDays = (int) $endDate->diff($startDate)->format("%a");
        return 'W#' . (string) (floor($totalDays / 7) + ($totalDays % 7 ? 0 : 1));
    }
}
