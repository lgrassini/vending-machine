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
}
