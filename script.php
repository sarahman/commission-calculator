<?php

declare(strict_types=1);

use GuzzleHttp\Client as GuzzleHttpClient;
use Sarahman\CommissionTask\CommissionCalculator;
use Sarahman\CommissionTask\CommissionRule\DepositRule;
use Sarahman\CommissionTask\CommissionRule\WithdrawBusinessRule;
use Sarahman\CommissionTask\CommissionRule\WithdrawPrivateRule;
use Sarahman\CommissionTask\Exception\CalculationException;
use Sarahman\CommissionTask\Service\DataReader\CsvDataReader;
use Sarahman\CommissionTask\Service\ExchangeRate\CurrencyExchangeApiClient;
use Sarahman\CommissionTask\Service\ExchangeRate\CurrencyExchangeApiException;

require_once __DIR__ . '/vendor/autoload.php';

$dotEnv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotEnv->load();

$source = isset($argv[1]) ? $argv[1] : $_ENV['CSV_URL'];

if (!file_exists($source)) {
    exit(sprintf('Sorry; the file %s is not existed!', $source) . PHP_EOL);
}

$csvDataReader = new CsvDataReader($source);
$httpClient = new GuzzleHttpClient(['base_uri' => $_ENV['EXCHANGE_RATE_URL']]);
$currencyExchangeClient = new CurrencyExchangeApiClient($httpClient, $_ENV['EXCHANGE_ACCESS_KEY']);

try {
    $exchangeRates = $currencyExchangeClient->getRates();
} catch (CurrencyExchangeApiException $exception) {
    exit($exception->getMessage() . PHP_EOL);
}

$rules = [
    new DepositRule(0.03),
    new WithdrawBusinessRule(0.5),
    new WithdrawPrivateRule(0.3, 'EUR', $exchangeRates, 1000, 3),
];

$commissionCalculator = new CommissionCalculator($csvDataReader, $rules);

try {
    $commissions = $commissionCalculator->calculate();
} catch (CalculationException $exception) {
    exit($exception->getMessage() . PHP_EOL);
}

echo join(PHP_EOL, $commissions) . PHP_EOL;
