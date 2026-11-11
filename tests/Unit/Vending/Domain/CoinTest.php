<?php

declare(strict_types=1);

namespace App\Tests\Unit\Vending\Domain;

use App\Vending\Domain\Coin;
use App\Vending\Domain\Money;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CoinTest extends TestCase
{
    #[Test]
    public function it_is_worth_its_value_in_money(): void
    {
        self::assertEquals(Money::fromCents(25), Coin::TwentyFiveCents->money());
    }

    #[Test]
    public function it_never_returns_1_coins(): void
    {
        self::assertFalse(Coin::One->isReturnable());
        self::assertTrue(Coin::FiveCents->isReturnable());
        self::assertTrue(Coin::TenCents->isReturnable());
        self::assertTrue(Coin::TwentyFiveCents->isReturnable());
    }
}
