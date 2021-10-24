<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Tests;

use PHPUnit\Framework\TestCase;
use Sarahman\CommissionTask\CommissionCalculator;
use Sarahman\CommissionTask\CommissionRules\DepositRule;
use Sarahman\CommissionTask\CommissionRules\WithdrawBusinessRule;
use Sarahman\CommissionTask\CommissionRules\WithdrawPrivateRule;
use Sarahman\CommissionTask\Service\DataReader\InputData;
use Sarahman\CommissionTask\Service\ExchangeRate\Client;
use Sarahman\CommissionTask\Transactions\Collection as TransactionCollection;

class CommissionCalculatorTest extends TestCase
{
    public function testCommissionWhenDataIsEmpty()
    {
        $exchangeClientObj = $this->mockExchangeRateClient();
        $exchangeClientObj->method('getRate')->willReturn(1.00);

        $rules = [
            new DepositRule(),
            new WithdrawBusinessRule(),
            new WithdrawPrivateRule($exchangeClientObj)
        ];

        $manager = new CommissionCalculator(new TransactionCollection([]), $rules);
        $commissions = $manager->process();

        $this->assertInternalType('array', $commissions);
        $this->assertEquals(0, count($commissions));
    }

    /**
     * @param $transactionDate
     * @param $userId
     * @param $userType
     * @param $operationType
     * @param $amount
     * @param $currency
     * @param $commission
     * @param $rate
     * @dataProvider dataProviderForAddTesting
     */
    public function testEveryTransactionWithMatchingInputAndOutput($transactionDate, $userId, $userType, $operationType, $amount, $currency, $commission, $rate)
    {
        $exchangeClientObj = $this->mockExchangeRateClient();
        $exchangeClientObj->method('getRate')->willReturn($rate);

        $rules = [
            new DepositRule(),
            new WithdrawBusinessRule(),
            new WithdrawPrivateRule($exchangeClientObj)
        ];

        $collection = new TransactionCollection([
            $this->getSampleTransaction($transactionDate, $userId, $userType, $operationType, $amount, $currency)
        ]);

        $manager = new CommissionCalculator($collection, $rules);
        $commissions = $manager->process();

        $this->assertInternalType('array', $commissions);
        $this->assertEquals(1, count($commissions));
        $this->assertEquals($commission, round($commissions[0], 2));
    }

    public function testAllTransactionsWithMatchingInputAndOutput()
    {
        $transactions = [];
        $expectedCommissions = [];
        $currencyMapping = [];
        foreach (new InputOutputFileIterator('./input.csv', './output.csv') as $transaction) {
            $transactions[] = $this->getSampleTransaction($transaction[0], $transaction[1], $transaction[2], $transaction[3], $transaction[4], $transaction[5]);

            $currencyMapping[] = [$transaction[5], true, $transaction[7]];

            $expectedCommissions[] = $transaction[6];
        }

        $exchangeClientObj = $this->mockExchangeRateClient();
        $exchangeClientObj->method('getRate')->will($this->returnValueMap($currencyMapping));

        $rules = [
            new DepositRule(),
            new WithdrawBusinessRule(),
            new WithdrawPrivateRule($exchangeClientObj)
        ];

        $collection = new TransactionCollection($transactions);

        $manager = new CommissionCalculator($collection, $rules);
        $commissions = $manager->process();

        $this->assertInternalType('array', $commissions);
        $this->assertEquals(13, count($commissions));

        foreach ($commissions as $key => $commission) {
            $this->assertEquals($expectedCommissions[$key], $commission, 'Transaction #' . ($key + 1));
        }
    }

    public function dataProviderForAddTesting(): InputOutputFileIterator
    {
        return new InputOutputFileIterator('./input.csv', './output2.csv');
    }

    /**
     * @param $transactionDate
     * @param $userId
     * @param $userType
     * @param $operationType
     * @param $amount
     * @param $currency
     * @return InputData
     */
    private function getSampleTransaction($transactionDate, $userId, $userType, $operationType, $amount, $currency): InputData
    {
        $inputObject = new InputData();
        $inputObject->setTransactionDate($transactionDate);
        $inputObject->setUserIdentification($userId);
        $inputObject->setUserType($userType);
        $inputObject->setOperationType($operationType);
        $inputObject->setOperationAmount((float) $amount);
        $inputObject->setOperationCurrency($currency);
        return $inputObject;
    }

    /**
     * @return Client | \PHPUnit\Framework\MockObject\MockObject
     */
    private function mockExchangeRateClient()
    {
        return $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRate'])
            ->getMock();
    }
}
