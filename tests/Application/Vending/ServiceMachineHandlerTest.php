<?php

declare(strict_types=1);

namespace App\Tests\Application\Vending;

use App\Vending\Application\ServiceMachine;
use App\Vending\Application\ServiceMachineHandler;
use App\Vending\Domain\Coin;
use App\Vending\Domain\Coins;
use App\Vending\Domain\Item;
use App\Vending\Domain\MachineServiced;
use App\Vending\Domain\Money;
use App\Vending\Domain\Selector;
use App\Vending\Infrastructure\InMemoryVendingMachineRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ServiceMachineHandlerTest extends TestCase
{
    #[Test]
    public function it_services_the_machine_and_saves_it(): void
    {
        $repository = new InMemoryVendingMachineRepository();
        $handler = new ServiceMachineHandler($repository);

        $events = $handler(new ServiceMachine(
            Coins::of(Coin::TenCents),
            new Item(new Selector('WATER'), Money::fromCents(65), 5),
        ));

        self::assertEquals([new MachineServiced()], $events);
        self::assertSame(1, $repository->get()->availableChange()->count(Coin::TenCents));
    }
}
