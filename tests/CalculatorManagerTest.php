<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\Tests;

use PHPUnit\Framework\TestCase;
use Paysera\CommissionTask\CalculatorManager;
use Paysera\CommissionTask\CommissionRules\DepositRule;
use Paysera\CommissionTask\CommissionRules\WithdrawBusinessRule;
use Paysera\CommissionTask\CommissionRules\WithdrawPrivateRule;
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
