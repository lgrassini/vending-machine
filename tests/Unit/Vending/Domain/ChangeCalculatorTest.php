<?php

declare(strict_types=1);

namespace App\Tests\Unit\Vending\Domain;

use App\Vending\Domain\ChangeCalculator;
use App\Vending\Domain\Coin;
use App\Vending\Domain\Money;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ChangeCalculatorTest extends TestCase
{
    #[Test]
    public function it_gives_change_with_a_single_coin(): void
    {
        $change = new ChangeCalculator()->calculate(Money::fromCents(25), [Coin::TwentyFiveCents]);

        self::assertSame([Coin::TwentyFiveCents], $change);
    }

    #[Test]
    public function it_gives_only_the_coins_needed(): void
    {
        $change = new ChangeCalculator()->calculate(Money::fromCents(10), [Coin::TwentyFiveCents, Coin::TenCents]);

        self::assertSame([Coin::TenCents], $change);
    }

    #[Test]
    public function it_is_not_greedy(): void
    {
        $change = new ChangeCalculator()->calculate(
            Money::fromCents(30),
            [Coin::TwentyFiveCents, Coin::TenCents, Coin::TenCents, Coin::TenCents],
        );

        self::assertSame([Coin::TenCents, Coin::TenCents, Coin::TenCents], $change);
    }

    #[Test]
    public function it_gives_nothing_when_exact_change_is_impossible(): void
    {
        self::assertNull(new ChangeCalculator()->calculate(Money::fromCents(5), [Coin::TenCents]));
    }

    #[Test]
    public function it_gives_no_coins_for_zero(): void
    {
        self::assertSame([], new ChangeCalculator()->calculate(Money::fromCents(0), [Coin::TenCents]));
    }
}
