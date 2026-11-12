<?php

declare(strict_types=1);

namespace App\Vending\Domain;

/** Coins held by the machine, counted per Coin (e.g. Available Change, Cash Box). */
final readonly class Coins
{
    /** @param array<int, int> $countPerCoin keyed by Coin value (enums can't be array keys) */
    private function __construct(private array $countPerCoin) {}

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
        $countPerCoin = $this->countPerCoin;
        $countPerCoin[$coin->value] = $this->count($coin) + 1;

        return new self($countPerCoin);
    }

    public function count(Coin $coin): int
    {
        return $this->countPerCoin[$coin->value] ?? 0;
    }

    /** @return list<Coin> */
    public function toList(): array
    {
        $list = [];

        foreach ($this->countPerCoin as $value => $count) {
            for ($i = 0; $i < $count; $i++) {
                $list[] = Coin::from($value);
            }
        }

        return $list;
    }
}
