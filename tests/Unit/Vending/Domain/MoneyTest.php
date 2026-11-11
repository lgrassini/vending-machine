<?php

declare(strict_types=1);

namespace App\Tests\Unit\Vending\Domain;

use App\Vending\Domain\Money;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    #[Test]
    public function it_is_created_from_cents(): void
    {
        self::assertSame(65, Money::fromCents(65)->cents());
    }

    #[Test]
    public function it_is_never_negative(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Money::fromCents(-5);
    }

    #[Test]
    public function it_adds_money(): void
    {
        self::assertSame(35, Money::fromCents(25)->add(Money::fromCents(10))->cents());
    }

    #[Test]
    public function it_subtracts_money(): void
    {
        self::assertSame(40, Money::fromCents(65)->subtract(Money::fromCents(25))->cents());
    }

    #[Test]
    public function it_is_less_than_another_amount(): void
    {
        self::assertTrue(Money::fromCents(25)->isLessThan(Money::fromCents(65)));
        self::assertFalse(Money::fromCents(65)->isLessThan(Money::fromCents(65)));
    }
}
