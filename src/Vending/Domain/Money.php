<?php

declare(strict_types=1);

namespace App\Vending\Domain;

/** An amount in integer cents, so arithmetic is exact (no floating-point rounding). */
final readonly class Money
{
    private function __construct(private int $cents)
    {
        if ($cents < 0) {
            throw new \InvalidArgumentException("Money can't be negative, got {$cents} cents.");
        }
    }

    public static function fromCents(int $cents): self
    {
        return new self($cents);
    }

    public function add(self $other): self
    {
        return new self($this->cents + $other->cents);
    }

    public function cents(): int
    {
        return $this->cents;
    }
}
