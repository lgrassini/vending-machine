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
}
