<?php

declare(strict_types=1);

namespace App\Tests\Unit\Vending\Domain;

use App\Vending\Domain\ChangeDispensed;
use App\Vending\Domain\Coin;
use App\Vending\Domain\CoinInserted;
use App\Vending\Domain\CoinRejected;
use App\Vending\Domain\CoinRejectionReason;
use App\Vending\Domain\Coins;
use App\Vending\Domain\CoinsReturned;
use App\Vending\Domain\Item;
use App\Vending\Domain\ItemDispensed;
use App\Vending\Domain\Money;
use App\Vending\Domain\PurchaseRejected;
use App\Vending\Domain\PurchaseRejectionReason;
use App\Vending\Domain\Selector;
use App\Vending\Domain\VendingMachine;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class VendingMachineTest extends TestCase
{
    #[Test]
    public function it_adds_an_inserted_coin_to_the_balance(): void
    {
        $machine = new VendingMachine(Coins::empty());

        $machine->insertCoin(Money::fromCents(25));

        self::assertEquals(Money::fromCents(25), $machine->balance());
    }

    #[Test]
    public function it_records_coin_inserted(): void
    {
        $machine = new VendingMachine(Coins::empty());

        $machine->insertCoin(Money::fromCents(25));

        self::assertEquals([new CoinInserted(Coin::TwentyFiveCents)], $machine->releaseEvents());
    }

    #[Test]
    public function it_keeps_inserted_change_coins_as_available_change(): void
    {
        $machine = new VendingMachine(Coins::empty());

        $machine->insertCoin(Money::fromCents(25));

        self::assertSame(1, $machine->availableChange()->count(Coin::TwentyFiveCents));
    }

    #[Test]
    public function it_keeps_inserted_1_coins_in_the_cash_box(): void
    {
        $machine = new VendingMachine(
            Coins::of(Coin::TwentyFiveCents, Coin::TwentyFiveCents, Coin::TwentyFiveCents, Coin::TwentyFiveCents),
        );

        $machine->insertCoin(Money::fromCents(100));

        self::assertSame(1, $machine->cashBox()->count(Coin::One));
        self::assertSame(0, $machine->availableChange()->count(Coin::One));
    }

    #[Test]
    public function it_rejects_a_1_coin_it_could_not_refund(): void
    {
        $machine = new VendingMachine(Coins::empty());

        $machine->insertCoin(Money::fromCents(100));

        self::assertEquals(
            [new CoinRejected(Money::fromCents(100), CoinRejectionReason::RefundNotGuaranteed)],
            $machine->releaseEvents(),
        );
        self::assertTrue($machine->balance()->isZero());
    }

    #[Test]
    public function it_rejects_an_invalid_coin(): void
    {
        $machine = new VendingMachine(Coins::empty());

        $machine->insertCoin(Money::fromCents(1));

        self::assertEquals(
            [new CoinRejected(Money::fromCents(1), CoinRejectionReason::InvalidCoin)],
            $machine->releaseEvents(),
        );
        self::assertTrue($machine->balance()->isZero());
    }

    #[Test]
    public function it_returns_the_balance_in_coins(): void
    {
        $machine = new VendingMachine(Coins::empty());
        $machine->insertCoin(Money::fromCents(25));
        $machine->insertCoin(Money::fromCents(10));
        $machine->releaseEvents();

        $machine->returnCoin();

        self::assertEquals([new CoinsReturned([Coin::TwentyFiveCents, Coin::TenCents])], $machine->releaseEvents());
        self::assertTrue($machine->balance()->isZero());
    }

    #[Test]
    public function it_returns_a_1_coin_in_smaller_coins(): void
    {
        $machine = new VendingMachine(
            Coins::of(Coin::TwentyFiveCents, Coin::TwentyFiveCents, Coin::TwentyFiveCents, Coin::TwentyFiveCents),
        );
        $machine->insertCoin(Money::fromCents(100));
        $machine->releaseEvents();

        $machine->returnCoin();

        self::assertEquals(
            [new CoinsReturned([Coin::TwentyFiveCents, Coin::TwentyFiveCents, Coin::TwentyFiveCents, Coin::TwentyFiveCents])],
            $machine->releaseEvents(),
        );
    }

    #[Test]
    public function it_removes_returned_coins_from_available_change(): void
    {
        $machine = new VendingMachine(Coins::empty());
        $machine->insertCoin(Money::fromCents(25));

        $machine->returnCoin();

        self::assertSame(0, $machine->availableChange()->count(Coin::TwentyFiveCents));
    }

    #[Test]
    public function it_dispenses_an_item(): void
    {
        $machine = new VendingMachine(Coins::empty(), new Item(new Selector('WATER'), Money::fromCents(25), 1));
        $machine->insertCoin(Money::fromCents(25));
        $machine->releaseEvents();

        $machine->selectItem(new Selector('WATER'));

        self::assertEquals([new ItemDispensed(new Selector('WATER'))], $machine->releaseEvents());
        self::assertTrue($machine->balance()->isZero());
    }

    #[Test]
    public function it_reduces_the_stock_of_the_dispensed_item(): void
    {
        $water = new Item(new Selector('WATER'), Money::fromCents(25), 2);
        $machine = new VendingMachine(Coins::empty(), $water);
        $machine->insertCoin(Money::fromCents(25));

        $machine->selectItem(new Selector('WATER'));

        self::assertSame(1, $water->stock);
    }

    #[Test]
    public function it_dispenses_change_when_the_balance_exceeds_the_price(): void
    {
        $machine = new VendingMachine(
            Coins::of(Coin::TenCents),
            new Item(new Selector('WATER'), Money::fromCents(65), 1),
        );
        $machine->insertCoin(Money::fromCents(25));
        $machine->insertCoin(Money::fromCents(25));
        $machine->insertCoin(Money::fromCents(25));
        $machine->releaseEvents();

        $machine->selectItem(new Selector('WATER'));

        self::assertEquals(
            [new ItemDispensed(new Selector('WATER')), new ChangeDispensed([Coin::TenCents])],
            $machine->releaseEvents(),
        );
        self::assertTrue($machine->balance()->isZero());
    }

    #[Test]
    public function it_removes_dispensed_change_from_available_change(): void
    {
        $machine = new VendingMachine(
            Coins::of(Coin::TenCents),
            new Item(new Selector('WATER'), Money::fromCents(65), 1),
        );
        $machine->insertCoin(Money::fromCents(25));
        $machine->insertCoin(Money::fromCents(25));
        $machine->insertCoin(Money::fromCents(25));

        $machine->selectItem(new Selector('WATER'));

        self::assertSame(0, $machine->availableChange()->count(Coin::TenCents));
    }

    #[Test]
    public function it_rejects_an_unknown_item(): void
    {
        $machine = new VendingMachine(Coins::empty());
        $machine->insertCoin(Money::fromCents(25));
        $machine->releaseEvents();

        $machine->selectItem(new Selector('COFFEE'));

        self::assertEquals(
            [new PurchaseRejected(new Selector('COFFEE'), PurchaseRejectionReason::UnknownItem)],
            $machine->releaseEvents(),
        );
        self::assertEquals(Money::fromCents(25), $machine->balance());
    }

    #[Test]
    public function it_rejects_an_item_out_of_stock(): void
    {
        $machine = new VendingMachine(Coins::empty(), new Item(new Selector('WATER'), Money::fromCents(25), 0));
        $machine->insertCoin(Money::fromCents(25));
        $machine->releaseEvents();

        $machine->selectItem(new Selector('WATER'));

        self::assertEquals(
            [new PurchaseRejected(new Selector('WATER'), PurchaseRejectionReason::OutOfStock)],
            $machine->releaseEvents(),
        );
        self::assertEquals(Money::fromCents(25), $machine->balance());
    }

    #[Test]
    public function it_rejects_a_purchase_with_insufficient_funds(): void
    {
        $machine = new VendingMachine(Coins::empty(), new Item(new Selector('WATER'), Money::fromCents(65), 1));
        $machine->insertCoin(Money::fromCents(25));
        $machine->releaseEvents();

        $machine->selectItem(new Selector('WATER'));

        self::assertEquals(
            [new PurchaseRejected(new Selector('WATER'), PurchaseRejectionReason::InsufficientFunds)],
            $machine->releaseEvents(),
        );
        self::assertEquals(Money::fromCents(25), $machine->balance());
    }
}
