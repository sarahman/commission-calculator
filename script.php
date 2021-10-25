<?php

declare(strict_types=1);

use GuzzleHttp\Client as GuzzleHttpClient;
use Sarahman\CommissionTask\CommissionCalculator;
use Sarahman\CommissionTask\CommissionRule\DepositRule;
use Sarahman\CommissionTask\CommissionRule\WithdrawBusinessRule;
use Sarahman\CommissionTask\CommissionRule\WithdrawPrivateRule;
use Sarahman\CommissionTask\Service\DataReader\CsvDataReader;
use Sarahman\CommissionTask\Service\ExchangeRate\Client;
use Sarahman\CommissionTask\Service\History\WeeklyHistory;

require_once __DIR__ . '/vendor/autoload.php';

$dotEnv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotEnv->load();

$source = empty($argv[1]) ? $_ENV['CSV_URL'] : $argv[1];

if (!file_exists($source)) {
    exit('Sorry; the file' . $source . ' is not existed!' . PHP_EOL);
}

$collection = new CsvDataReader($source);
$exchangeClientObj = new Client(new GuzzleHttpClient(['base_uri' => $_ENV['EXCHANGE_RATE_URL']]), $_ENV['EXCHANGE_ACCESS_KEY']);
$rules = [
    new DepositRule(),
    new WithdrawBusinessRule(),
    new WithdrawPrivateRule($exchangeClientObj, new WeeklyHistory()),
];

$calculator = new CommissionCalculator($collection, $rules);
$commissions = $calculator->calculate();

print join(PHP_EOL, $commissions) . PHP_EOL;
