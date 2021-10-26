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

class CommissionCalculatorTest extends TestCase
{
    public function testAllTransactionsWithMatchingInputAndOutput()
    {
        $dataReader = new CsvDataReader('./input.csv');
        $exchangeRates = [
            'EUR' => 1.0,
            'USD' => 1.1497,
            'JPY' => 129.53,
        ];
        $rules = [
            new DepositRule(0.03),
            new WithdrawBusinessRule(0.5),
            new WithdrawPrivateRule(0.3, 'EUR', $exchangeRates, 1000, 3),
        ];

        $calculator = new CommissionCalculator($dataReader, $rules);
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
