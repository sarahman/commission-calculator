<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Tests;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use PHPUnit\Framework\TestCase;
use Sarahman\CommissionTask\CommissionCalculator;
use Sarahman\CommissionTask\CommissionRule\DepositRule;
use Sarahman\CommissionTask\CommissionRule\WithdrawBusinessRule;
use Sarahman\CommissionTask\CommissionRule\WithdrawPrivateRule;

class CommissionCalculatorTest extends TestCase
{
    use ArraySubsetAsserts;

    private array $rules;

    public function setUp(): void
    {
        parent::setUp();

        $exchangeRates = [
            'EUR' => 1.0,
            'USD' => 1.1497,
            'JPY' => 129.53,
        ];

        $this->rules = [
            new DepositRule(0.03),
            new WithdrawBusinessRule(0.5),
            new WithdrawPrivateRule(0.3, 'EUR', $exchangeRates, 1000, 3),
        ];
    }

    /**
     * @dataProvider getBulkTransactionalInputsAndOutputs
     */
    public function testAllTransactionsWithMatchingInputAndOutput(array $input, array $output)
    {
        $dataReader = new ArrayIterator($input);
        $calculator = new CommissionCalculator($dataReader, $this->rules);
        $commissions = $calculator->calculate();

        $this->assertIsArray($commissions);
        $this->assertEquals(13, count($commissions));
        $this->assertArraySubset($commissions, $output);
        $this->assertEquals($commissions, $output);
    }

    /**
     * @dataProvider getEveryTransactionalInputsAndOutput
     */
    public function testSingleTransactionWithMatchingInputAndOutput(
        string $date,
        string $userIdentification,
        string $userType,
        string $operationType,
        string $operationAmount,
        string $operationCurrency,
        string $expectedCommission
    ) {
        $dataReader = new ArrayIterator([
            [
                $date,
                $userIdentification,
                $userType,
                $operationType,
                (float) $operationAmount,
                $operationCurrency
            ]
        ]);

        $calculator = new CommissionCalculator($dataReader, $this->rules);
        $commissions = $calculator->calculate();

        $this->assertIsArray($commissions);
        $this->assertEquals(1, count($commissions));
        $this->assertEquals($expectedCommission, $commissions[0]);
    }

    public function getBulkTransactionalInputsAndOutputs()
    {
        return [
            [
                [
                    ['2014-12-31', '4', 'private', 'withdraw', '1200.00', 'EUR'],
                    ['2015-01-01', '4', 'private', 'withdraw', '1000.00', 'EUR'],
                    ['2016-01-05', '4', 'private', 'withdraw', '1000.00', 'EUR'],
                    ['2016-01-05', '1', 'private', 'deposit', '200.00', 'EUR'],
                    ['2016-01-06', '2', 'business', 'withdraw', '300.00', 'EUR'],
                    ['2016-01-06', '1', 'private', 'withdraw', '30000', 'JPY'],
                    ['2016-01-07', '1', 'private', 'withdraw', '1000.00', 'EUR'],
                    ['2016-01-07', '1', 'private', 'withdraw', '100.00', 'USD'],
                    ['2016-01-10', '1', 'private', 'withdraw', '100.00', 'EUR'],
                    ['2016-01-10', '2', 'business', 'deposit', '10000.00', 'EUR'],
                    ['2016-01-10', '3', 'private', 'withdraw', '1000.00', 'EUR'],
                    ['2016-02-15', '1', 'private', 'withdraw', '300.00', 'EUR'],
                    ['2016-02-19', '5', 'private', 'withdraw', '3000000', 'JPY'],
                ],
                [
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
                ]
            ]
        ];
    }

    public function getEveryTransactionalInputsAndOutput()
    {
        return [
            ['2014-12-31', '4', 'private', 'withdraw', '1200.00', 'EUR', '0.60'],
            ['2015-01-01', '4', 'private', 'withdraw', '1000.00', 'EUR', '0.00'],
            ['2016-01-05', '4', 'private', 'withdraw', '1000.00', 'EUR', '0.00'],
            ['2016-01-05', '1', 'private', 'deposit', '200.00', 'EUR', '0.06'],
            ['2016-01-06', '2', 'business', 'withdraw', '300.00', 'EUR', '1.50'],
            ['2016-01-06', '1', 'private', 'withdraw', '30000', 'JPY', '0'],
            ['2016-01-07', '1', 'private', 'withdraw', '1000.00', 'EUR', '0.00'],
            ['2016-01-07', '1', 'private', 'withdraw', '100.00', 'USD', '0.00'],
            ['2016-01-10', '1', 'private', 'withdraw', '100.00', 'EUR', '0.00'],
            ['2016-01-10', '2', 'business', 'deposit', '10000.00', 'EUR', '3.00'],
            ['2016-01-10', '3', 'private', 'withdraw', '1000.00', 'EUR', '0.00'],
            ['2016-02-15', '1', 'private', 'withdraw', '300.00', 'EUR', '0.00'],
            ['2016-02-19', '5', 'private', 'withdraw', '3000000', 'JPY', '8612'],
        ];
    }
}
