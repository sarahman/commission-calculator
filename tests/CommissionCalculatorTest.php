<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sarahman\CommissionTask\CommissionCalculator;
use Sarahman\CommissionTask\CommissionRule\DepositRule;
use Sarahman\CommissionTask\CommissionRule\WithdrawBusinessRule;
use Sarahman\CommissionTask\CommissionRule\WithdrawPrivateRule;
use Sarahman\CommissionTask\Service\DataReader\CsvDataReader;
use Sarahman\CommissionTask\Service\ExchangeRate\Client;

class CommissionCalculatorTest extends TestCase
{
    /**
     * @var Client
     */
    private MockObject $exchangeClientObj;

    public function setUp(): void
    {
        parent::setUp();

        $this->exchangeClientObj = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getRate'])
            ->getMock()
        ;
    }

    public function testAllTransactionsWithMatchingInputAndOutput()
    {
        $this->exchangeClientObj->method('getRate')->will($this->returnValueMap([
            ['EUR', 1.0],
            ['USD', 1.1497],
            ['JPY', 129.53],
        ]));

        $collection = new CsvDataReader('./input.csv');
        $rules = [
            new DepositRule(),
            new WithdrawBusinessRule(),
            new WithdrawPrivateRule($this->exchangeClientObj),
        ];

        $calculator = new CommissionCalculator($collection, $rules);
        $commissions = $calculator->calculate();

        $this->assertIsArray($commissions);
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
}
