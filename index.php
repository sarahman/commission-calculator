<?php

declare(strict_types=1);

use Paysera\CommissionTask\CalculatorManager;
use Paysera\CommissionTask\Service\DataReader\CsvDataReader;
use Paysera\CommissionTask\Service\DataReader\CsvFormatter;
use Paysera\CommissionTask\Service\ExchangeRate\RateFormatter;
use Paysera\CommissionTask\Service\ExchangeRate\RateService;
use Paysera\CommissionTask\Transactions\Collection as TransactionCollection;

require_once './vendor/autoload.php';

$dotEnv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotEnv->load();

$rawData = (new CsvDataReader($_ENV['CSV_URL']))
    ->setFormatter(new CsvFormatter())
    ->parseData()
    ->getData();

$collection = new TransactionCollection($rawData);

$exchangeRateServiceObj = (new RateService($_ENV['EXCHANGE_RATE_URL'], $_ENV['EXCHANGE_ACCESS_KEY']))
    ->setFormatter(new RateFormatter());

$commissions = (new CalculatorManager())
    ->addTransactions($collection)
    ->applyAllRules()
    ->getCommissions();

print join(PHP_EOL, $commissions) . PHP_EOL;
