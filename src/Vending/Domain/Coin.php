<?php

declare(strict_types=1);

namespace App\Vending\Domain;

/** A coin the machine accepts, backed by its value in cents (see D-005). */
enum Coin: int
{
    case FiveCents = 5;
    case TenCents = 10;
    case TwentyFiveCents = 25;
    case One = 100;

    public function money(): Money
    {
        return Money::fromCents($this->value);
    }

    /** 1 coins go to the Cash Box and are never given back (D-001). */
    public function isReturnable(): bool
    {
        return match ($this) {
            self::FiveCents, self::TenCents, self::TwentyFiveCents => true,
            self::One => false,
        };
    }
}
