<?php

declare(strict_types=1);

use GuzzleHttp\Client as GuzzleHttpClient;
use Sarahman\CommissionTask\CommissionCalculator;
use Sarahman\CommissionTask\CommissionRule\DepositRule;
use Sarahman\CommissionTask\CommissionRule\WithdrawBusinessRule;
use Sarahman\CommissionTask\CommissionRule\WithdrawPrivateRule;
use Sarahman\CommissionTask\Service\DataReader\CsvDataReader;
use Sarahman\CommissionTask\Service\ExchangeRate\CurrencyExchangeApiClient;

require_once __DIR__ . '/vendor/autoload.php';

$dotEnv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotEnv->load();

$source = empty($argv[1]) ? $_ENV['CSV_URL'] : $argv[1];

if (!file_exists($source)) {
    exit('Sorry; the file' . $source . ' is not existed!' . PHP_EOL);
}

$csvDataReader = new CsvDataReader($source);
$httpClient = new GuzzleHttpClient(['base_uri' => $_ENV['EXCHANGE_RATE_URL']]);
$currencyExchangeClient = new CurrencyExchangeApiClient($httpClient, $_ENV['EXCHANGE_ACCESS_KEY']);
$exchangeRates = $currencyExchangeClient->getRates();
$rules = [
    new DepositRule(0.03),
    new WithdrawBusinessRule(0.5),
    new WithdrawPrivateRule(0.3, 'EUR', $exchangeRates, 1000, 3),
];

$commissionCalculator = new CommissionCalculator($csvDataReader, $rules);

try {
    $commissions = $commissionCalculator->calculate();
    exit($exception->getMessage() . PHP_EOL);
}

echo join(PHP_EOL, $commissions) . PHP_EOL;
