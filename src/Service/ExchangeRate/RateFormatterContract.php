<?php

declare(strict_types=1);

namespace Paysera\CommissionTask\Service\ExchangeRate;

interface RateFormatterContract
{
    /**
     * @param array $rates
     * @param string $currency
     * @return float
     */
    public function format(array $rates, string $currency): float;
}
