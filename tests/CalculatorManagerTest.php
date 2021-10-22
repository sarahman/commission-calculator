<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\Tests;

use PHPUnit\Framework\TestCase;
use Paysera\CommissionTask\CalculatorManager;
use Paysera\CommissionTask\CommissionRules\DepositRule;
use Paysera\CommissionTask\CommissionRules\WithdrawBusinessRule;
use Paysera\CommissionTask\CommissionRules\WithdrawPrivateRule;
use Paysera\CommissionTask\Service\DataReader\InputData;
use Paysera\CommissionTask\Service\ExchangeRate\RateService;
use Paysera\CommissionTask\Transactions\Collection as TransactionCollection;

class CalculatorManagerTest extends TestCase
{
    public function testCommissionWhenDataIsEmpty()
    {
        $rateService = $this->mockRateService();
        $rateService->method('getRate')->willReturn(1.00);

        $manager = new CalculatorManager();

        $manager->addTransactions(new TransactionCollection([]))
            ->addRule(new DepositRule())
            ->addRule(new WithdrawBusinessRule())
            ->addRule(new WithdrawPrivateRule($rateService))
            ->applyAllRules();

        $this->assertEquals(0, count($manager->getAllTransactions()));
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
        $rateService = $this->mockRateService();
        $rateService->method('getRate')->willReturn($rate);

        $collection = new TransactionCollection([
            $this->getSampleTransaction($transactionDate, $userId, $userType, $operationType, $amount, $currency)
        ]);

        $manager = new CalculatorManager();
        $calculateTransaction = $manager->addTransactions($collection)
            ->addRule(new DepositRule())
            ->addRule(new WithdrawBusinessRule())
            ->addRule(new WithdrawPrivateRule($rateService))
            ->applyAllRules()
            ->getAllTransactions();

        $this->assertEquals(1, count($calculateTransaction));
        $this->assertEquals($commission, round($calculateTransaction[0]->getCommission(), 2));
    }

    public function dataProviderForAddTesting(): InputOutputFileIterator
    {
        return new InputOutputFileIterator('./input.csv', './output.csv');
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
    public function getSampleTransaction($transactionDate, $userId, $userType, $operationType, $amount, $currency): InputData
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
     * @return RateService | \PHPUnit\Framework\MockObject\MockObject
     */
    private function mockRateService()
    {
        return $this->getMockBuilder(RateService::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRate'])
            ->getMock();
    }
}
