<?php

declare(strict_types=1);

namespace App\Tests\Unit\Vending\Domain;

use App\Vending\Domain\Coin;
use App\Vending\Domain\CoinInserted;
use App\Vending\Domain\CoinRejected;
use App\Vending\Domain\CoinRejectionReason;
use App\Vending\Domain\Coins;
use App\Vending\Domain\Money;
use App\Vending\Domain\VendingMachine;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class VendingMachineTest extends TestCase
{
    #[Test]
    public function it_adds_an_inserted_coin_to_the_balance(): void
    {
        $machine = new VendingMachine(Coins::empty());

        $machine->insertCoin(Coin::TwentyFiveCents);

        self::assertEquals(Money::fromCents(25), $machine->balance());
    }

    #[Test]
    public function it_records_coin_inserted(): void
    {
        $machine = new VendingMachine(Coins::empty());

        $machine->insertCoin(Coin::TwentyFiveCents);

        self::assertEquals([new CoinInserted(Coin::TwentyFiveCents)], $machine->releaseEvents());
    }

    #[Test]
    public function it_keeps_inserted_change_coins_as_available_change(): void
    {
        $machine = new VendingMachine(Coins::empty());

        $machine->insertCoin(Coin::TwentyFiveCents);

        self::assertSame(1, $machine->availableChange()->count(Coin::TwentyFiveCents));
    }

    #[Test]
    public function it_keeps_inserted_1_coins_in_the_cash_box(): void
    {
        $machine = new VendingMachine(
            Coins::of(Coin::TwentyFiveCents, Coin::TwentyFiveCents, Coin::TwentyFiveCents, Coin::TwentyFiveCents),
        );

        $machine->insertCoin(Coin::One);

        self::assertSame(1, $machine->cashBox()->count(Coin::One));
        self::assertSame(0, $machine->availableChange()->count(Coin::One));
    }

    #[Test]
    public function it_rejects_a_1_coin_it_could_not_refund(): void
    {
        $machine = new VendingMachine(Coins::empty());

        $machine->insertCoin(Coin::One);

        self::assertEquals(
            [new CoinRejected(Coin::One, CoinRejectionReason::RefundNotGuaranteed)],
            $machine->releaseEvents(),
        );
        self::assertTrue($machine->balance()->isZero());
    }
}
