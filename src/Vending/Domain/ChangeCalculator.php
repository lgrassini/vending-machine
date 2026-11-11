<?php

declare(strict_types=1);

namespace App\Vending\Domain;

/** Finds the coins that make up an amount exactly, using only the Available Change. */
final class ChangeCalculator
{
    /**
     * @param list<Coin> $availableChange
     * @return list<Coin>
     */
    public function calculate(Money $change, array $availableChange): array
    {
        $coins = [];
        $remaining = $change;

        foreach ($availableChange as $coin) {
            if (!$remaining->isLessThan($coin->money())) {
                $coins[] = $coin;
                $remaining = $remaining->subtract($coin->money());
            }
        }

        return $coins;
    }
}
