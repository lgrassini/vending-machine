<?php

declare(strict_types=1);

namespace App\Tests\Application\Vending;

use App\Vending\Application\GetMachineStatus;
use App\Vending\Application\GetMachineStatusHandler;
use App\Vending\Application\InsertCoin;
use App\Vending\Application\InsertCoinHandler;
use App\Vending\Application\ItemStatus;
use App\Vending\Application\MachineStatus;
use App\Vending\Application\ServiceMachine;
use App\Vending\Application\ServiceMachineHandler;
use App\Vending\Domain\Coin;
use App\Vending\Domain\Coins;
use App\Vending\Domain\Item;
use App\Vending\Domain\Money;
use App\Vending\Domain\Selector;
use App\Vending\Infrastructure\InMemoryVendingMachineRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class GetMachineStatusHandlerTest extends TestCase
{
    #[Test]
    public function it_reports_the_machine_status(): void
    {
        $repository = new InMemoryVendingMachineRepository();
        new ServiceMachineHandler($repository)(new ServiceMachine(
            Coins::of(Coin::TenCents),
            new Item(new Selector('WATER'), Money::fromCents(65), 5),
        ));
        new InsertCoinHandler($repository)(new InsertCoin(Money::fromCents(25)));

        $status = new GetMachineStatusHandler($repository)(new GetMachineStatus());

        self::assertEquals(
            new MachineStatus(
                [new ItemStatus(new Selector('WATER'), Money::fromCents(65), 5)],
                Money::fromCents(25),
                Coins::of(Coin::TenCents, Coin::TwentyFiveCents),
                Coins::empty(),
            ),
            $status,
        );
    }

    #[Test]
    public function it_reports_the_cash_box(): void
    {
        $repository = new InMemoryVendingMachineRepository();
        new ServiceMachineHandler($repository)(new ServiceMachine(
            Coins::of(Coin::TwentyFiveCents, Coin::TwentyFiveCents, Coin::TwentyFiveCents, Coin::TwentyFiveCents),
        ));
        new InsertCoinHandler($repository)(new InsertCoin(Money::fromCents(100)));

        $status = new GetMachineStatusHandler($repository)(new GetMachineStatus());

        self::assertEquals(Coins::of(Coin::One), $status->cashBox);
    }
}
