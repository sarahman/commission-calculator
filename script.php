<?php

declare(strict_types=1);

use Sarahman\CommissionTask\CommissionCalculator;
use Sarahman\CommissionTask\CommissionRules\DepositRule;
use Sarahman\CommissionTask\CommissionRules\WithdrawBusinessRule;
use Sarahman\CommissionTask\CommissionRules\WithdrawPrivateRule;
use Sarahman\CommissionTask\Service\DataReader\CsvDataReader;
use Sarahman\CommissionTask\Service\DataReader\CsvFormatter;
use Sarahman\CommissionTask\Service\ExchangeRate\Client;
use Sarahman\CommissionTask\Transactions\Collection as TransactionCollection;

require_once __DIR__ . '/vendor/autoload.php';

$dotEnv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotEnv->load();

$rawData = (new CsvDataReader(empty($argv[1]) ? $_ENV['CSV_URL'] : $argv[1]))
    ->setFormatter(new CsvFormatter())
    ->parseData()
    ->getData();

$collection = new TransactionCollection($rawData);
$exchangeClientObj = (new Client($_ENV['EXCHANGE_RATE_URL'], $_ENV['EXCHANGE_ACCESS_KEY']));
$rules = [
    new DepositRule(),
    new WithdrawBusinessRule(),
    new WithdrawPrivateRule($exchangeClientObj)
];

$commissions = (new CommissionCalculator($collection, $rules))->process();

print join(PHP_EOL, $commissions) . PHP_EOL;
