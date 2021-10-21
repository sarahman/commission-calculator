<?php

declare(strict_types=1);

use Paysera\CommissionTask\Service\DataReader\CsvDataReader;
use Paysera\CommissionTask\Service\DataReader\CsvFormatter;

require_once './vendor/autoload.php';

$dotEnv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotEnv->load();

$rawData = (new CsvDataReader($_ENV['CSV_URL']))
    ->setFormatter(new CsvFormatter())
    ->parseData()
    ->getData();

var_dump($rawData);
die;
