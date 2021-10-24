<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Tests;

use PHPUnit\Framework\TestCase;
use Sarahman\CommissionTask\CommissionCalculator;
use Sarahman\CommissionTask\CommissionRules\DepositRule;
use Sarahman\CommissionTask\CommissionRules\WithdrawBusinessRule;
use Sarahman\CommissionTask\CommissionRules\WithdrawPrivateRule;
use Sarahman\CommissionTask\Service\DataReader\DataReader;
use Sarahman\CommissionTask\Service\DataReader\InputData;
use Sarahman\CommissionTask\Service\ExchangeRate\Client;

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

        $collection = (new DataReader(''));

        $calculator = new CommissionCalculator($collection, $rules);
        $commissions = $calculator->process();

        $this->assertInternalType('array', $commissions);
        $this->assertEquals(0, count($commissions));
    }

    public function testAllTransactionsWithMatchingInputAndOutput()
    {
        $exchangeClientObj = $this->mockExchangeRateClient();
        $exchangeClientObj->method('getRate')->will($this->returnValueMap([
            ['EUR', true, 1.0],
            ['USD', true, 1.1497],
            ['JPY', true, 129.53],
        ]));

        $rules = [
            new DepositRule(),
            new WithdrawBusinessRule(),
            new WithdrawPrivateRule($exchangeClientObj)
        ];

        $collection = (new DataReader('./input.csv'));

        $calculator = new CommissionCalculator($collection, $rules);
        $commissions = $calculator->process();

        $this->assertInternalType('array', $commissions);
        $this->assertEquals(13, count($commissions));

        $expectedCommissions = [
            '0.60',
            '3.00',
            '0.00',
            '0.06',
            '1.50',
            '0',
            '0.69',
            '0.30',
            '0.30',
            '3.00',
            '0.00',
            '0.00',
            '8612',
        ];

        foreach ($commissions as $key => $commission) {
            $this->assertEquals($expectedCommissions[$key], $commission, 'Transaction #' . ($key + 1));
        }
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
