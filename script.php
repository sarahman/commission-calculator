<?php

declare(strict_types=1);

use Sarahman\CommissionTask\CommissionCalculator;
use Sarahman\CommissionTask\CommissionRule\DepositRule;
use Sarahman\CommissionTask\CommissionRule\WithdrawBusinessRule;
use Sarahman\CommissionTask\CommissionRule\WithdrawPrivateRule;
use Sarahman\CommissionTask\Service\DataReader\DataFormatter;
use Sarahman\CommissionTask\Service\DataReader\DataReader;
use Sarahman\CommissionTask\Service\ExchangeRate\Client;
use Sarahman\CommissionTask\Service\ExchangeRate\RateFormatter;
use Sarahman\CommissionTask\Service\History\WeeklyHistory;

require_once __DIR__ . '/vendor/autoload.php';

$dotEnv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotEnv->load();

$collection = (new DataReader(empty($argv[1]) ? $_ENV['CSV_URL'] : $argv[1], new DataFormatter()));

$exchangeClientObj = (new Client($_ENV['EXCHANGE_RATE_URL'], $_ENV['EXCHANGE_ACCESS_KEY'], new RateFormatter()));
$rules = [
    new DepositRule(),
    new WithdrawBusinessRule(),
    new WithdrawPrivateRule($exchangeClientObj, new WeeklyHistory())
];

$commissions = (new CommissionCalculator($collection, $rules))->process();

print join(PHP_EOL, $commissions) . PHP_EOL;
