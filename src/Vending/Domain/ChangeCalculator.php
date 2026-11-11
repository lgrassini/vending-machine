<?php

declare(strict_types=1);

namespace App\Vending\Domain;

/** Finds the coins that make up an amount exactly, using only the Available Change (D-007). */
final class ChangeCalculator
{
    /**
     * @param list<Coin> $availableChange
     * @return list<Coin>|null null when no exact combination exists
     */
    public function calculate(Money $change, array $availableChange): ?array
    {
        usort($availableChange, static fn(Coin $a, Coin $b): int => $b->value <=> $a->value);

        return $this->search($change, $availableChange);
    }

    /**
     * @param list<Coin> $coins biggest first
     * @return list<Coin>|null
     */
    private function search(Money $remaining, array $coins): ?array
    {
        if ($remaining->isZero()) {
            return [];
        }

        foreach ($coins as $i => $coin) {
            if ($remaining->isLessThan($coin->money())) {
                continue;
            }

            $rest = $this->search($remaining->subtract($coin->money()), array_slice($coins, $i + 1));

            if ($rest !== null) {
                return [$coin, ...$rest];
            }
        }

        return null;
    }
}
