<?php

declare(strict_types=1);

namespace App\Vending\Domain;

/** Coins held by the machine, counted per Coin (e.g. Available Change, Cash Box). */
final readonly class Coins
{
    /** @param array<int, int> $counts count per Coin value (enums can't be array keys) */
    private function __construct(private array $counts) {}

    public static function empty(): self
    {
        return new self([]);
    }

    public static function of(Coin ...$coins): self
    {
        $collection = self::empty();

        foreach ($coins as $coin) {
            $collection = $collection->add($coin);
        }

        return $collection;
    }

    public function add(Coin $coin): self
    {
        $counts = $this->counts;
        $counts[$coin->value] = $this->count($coin) + 1;

        return new self($counts);
    }

    public function count(Coin $coin): int
    {
        return $this->counts[$coin->value] ?? 0;
    }
}
