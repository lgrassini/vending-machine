<?php

declare(strict_types=1);

namespace App\Tests\Unit\Vending\Domain;

use App\Vending\Domain\Coin;
use App\Vending\Domain\Coins;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CoinsTest extends TestCase
{
    #[Test]
    public function it_counts_coins_by_type(): void
    {
        $coins = Coins::empty()->add(Coin::TenCents)->add(Coin::TenCents);

        self::assertSame(2, $coins->count(Coin::TenCents));
    }

    #[Test]
    public function it_is_created_from_coins(): void
    {
        self::assertSame(2, Coins::of(Coin::TenCents, Coin::TenCents)->count(Coin::TenCents));
    }

    #[Test]
    public function it_removes_a_coin(): void
    {
        $coins = Coins::of(Coin::TenCents, Coin::TenCents)->remove(Coin::TenCents);

        self::assertSame(1, $coins->count(Coin::TenCents));
    }
}
