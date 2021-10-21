<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\Service\ExchangeRate;

interface RateContract
{
    /**
     * @param string $currency
     * @param bool $cache
     * @return float
     */
    public function getRate(string $currency, $cache = true): float;

    /**
     * @param RateFormatterContract $driver
     * @return mixed
     */
    public function setFormatter(RateFormatterContract $driver);
}
