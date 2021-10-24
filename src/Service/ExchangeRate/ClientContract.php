<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Service\ExchangeRate;

interface ClientContract
{
    /**
     * @param string $currency
     * @param bool $cache
     * @return float
     */
    public function getRate(string $currency, $cache = true): float;
}
