<?php

declare(strict_types=1);

use Sarahman\CommissionTask\CalculatorManager;
use Sarahman\CommissionTask\CommissionRules\DepositRule;
use Sarahman\CommissionTask\CommissionRules\WithdrawBusinessRule;
use Sarahman\CommissionTask\CommissionRules\WithdrawPrivateRule;
use Sarahman\CommissionTask\Service\DataReader\CsvDataReader;
use Sarahman\CommissionTask\Service\DataReader\CsvFormatter;
use Sarahman\CommissionTask\Service\ExchangeRate\RateFormatter;
use Sarahman\CommissionTask\Service\ExchangeRate\RateService;
use Sarahman\CommissionTask\Transactions\Collection as TransactionCollection;

require_once './vendor/autoload.php';

$dotEnv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotEnv->load();

$rawData = (new CsvDataReader(empty($argv[1]) ? $_ENV['CSV_URL'] : $argv[1]))
    ->setFormatter(new CsvFormatter())
    ->parseData()
    ->getData();

$collection = new TransactionCollection($rawData);

$exchangeRateServiceObj = (new RateService($_ENV['EXCHANGE_RATE_URL'], $_ENV['EXCHANGE_ACCESS_KEY']))
    ->setFormatter(new RateFormatter());

$commissions = (new CalculatorManager())
    ->addTransactions($collection)
    ->addRule(new DepositRule())
    ->addRule(new WithdrawBusinessRule())
    ->addRule(new WithdrawPrivateRule($exchangeRateServiceObj))
    ->applyAllRules()
    ->getCommissions();

print join(PHP_EOL, $commissions) . PHP_EOL;
