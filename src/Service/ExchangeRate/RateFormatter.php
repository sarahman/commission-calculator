<?php

declare(strict_types=1);

namespace Sarahman\CommissionTask\Service\ExchangeRate;

class RateFormatter implements RateFormatterContract
{
    /**
     * @param array $rates
     * @param string $currency
     * @return float
     */
    public function format(array $rates, string $currency): float
    {
        if (empty($rates['rates']) || empty($rates['rates'][$currency])) {
            return 0.00;
        }

        return floatval($rates['rates'][$currency]);
    }
}
