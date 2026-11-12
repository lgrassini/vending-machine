<?php

declare(strict_types=1);

namespace App\Vending\Infrastructure\Cli;

use App\Vending\Domain\Money;

/** Reads and writes Money in the spec's notation (0.25, 1), exactly, never through floats. */
final class MoneyText
{
    /** Returns null when the text is not an amount. */
    public static function parse(string $text): ?Money
    {
        // Checks that the text is an amount (e.g. 1 or 0.25) and captures its whole units and its decimals.
        if (preg_match('/^(\d+)(?:\.(\d{1,2}))?$/', $text, $matches) !== 1) {
            return null;
        }

        $fraction = str_pad($matches[2] ?? '', 2, '0');

        return Money::fromCents((int) $matches[1] * 100 + (int) $fraction);
    }

    public static function format(Money $money): string
    {
        $cents = $money->cents();

        return $cents % 100 === 0
            ? (string) intdiv($cents, 100)
            : sprintf('%d.%02d', intdiv($cents, 100), $cents % 100);
    }
}
