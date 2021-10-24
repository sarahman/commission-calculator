<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Service\ExchangeRate;

class RateFormatter
{
    /**
     * @param array $rates
     * @param string $currency
     * @return float
     */
    public function format(array $rates, string $currency): float
    {
        if (!isset($rates['rates']) || !isset($rates['rates'][$currency])) {
            return 0.00;
        }

        return (float) ($rates['rates'][$currency]);
    }
}
