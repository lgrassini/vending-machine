<?php

declare(strict_types=1);

namespace App\Tests\Unit\Vending\Infrastructure\Cli;

use App\Vending\Domain\Money;
use App\Vending\Infrastructure\Cli\MoneyText;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MoneyTextTest extends TestCase
{
    #[Test]
    public function it_parses_the_spec_notation(): void
    {
        self::assertEquals(Money::fromCents(25), MoneyText::parse('0.25'));
        self::assertEquals(Money::fromCents(10), MoneyText::parse('0.1'));
        self::assertEquals(Money::fromCents(100), MoneyText::parse('1'));
    }

    #[Test]
    public function it_rejects_text_that_is_not_an_amount(): void
    {
        self::assertNull(MoneyText::parse('GET-SODA'));
        self::assertNull(MoneyText::parse('0.255'));
        self::assertNull(MoneyText::parse('-1'));
        self::assertNull(MoneyText::parse(''));
    }

    #[Test]
    public function it_formats_in_the_spec_notation(): void
    {
        self::assertSame('0.10', MoneyText::format(Money::fromCents(10)));
        self::assertSame('1.50', MoneyText::format(Money::fromCents(150)));
        self::assertSame('1', MoneyText::format(Money::fromCents(100)));
    }
}
