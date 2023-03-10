<?php

declare(strict_types=1);

namespace Paywa\CommissionTask\Enum;

enum Currency: string
{
    case EUR = 'EUR';
    case USD = 'USD';
    case JPY = 'JPY';

    /**
     * There are zero-decimal, two-decimal and three-decimal currencies.
     * When adding new supported currency please check minor units and add here.
     * @see https://docs.adyen.com/development-resources/currency-codes
     *
     * @return int
     */
    public function decimalPlaces(): int
    {
        return match ($this) {
            self::EUR,
            self::USD => 2,
            self::JPY => 0,
        };
    }
}
