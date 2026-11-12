<?php

declare(strict_types=1);

namespace App\Tests\Application\Vending;

use App\Vending\Application\InsertCoin;
use App\Vending\Application\InsertCoinHandler;
use App\Vending\Application\SelectItem;
use App\Vending\Application\SelectItemHandler;
use App\Vending\Application\ServiceMachine;
use App\Vending\Application\ServiceMachineHandler;
use App\Vending\Domain\Coins;
use App\Vending\Domain\Item;
use App\Vending\Domain\ItemDispensed;
use App\Vending\Domain\Money;
use App\Vending\Domain\Selector;
use App\Vending\Infrastructure\InMemoryVendingMachineRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SelectItemHandlerTest extends TestCase
{
    #[Test]
    public function it_selects_an_item_and_saves_the_machine(): void
    {
        $repository = new InMemoryVendingMachineRepository();
        new ServiceMachineHandler($repository)(new ServiceMachine(
            Coins::empty(),
            new Item(new Selector('WATER'), Money::fromCents(25), 1),
        ));
        new InsertCoinHandler($repository)(new InsertCoin(Money::fromCents(25)));
        $handler = new SelectItemHandler($repository);

        $events = $handler(new SelectItem(new Selector('WATER')));

        self::assertEquals([new ItemDispensed(new Selector('WATER'))], $events);
        self::assertTrue($repository->get()->balance()->isZero());
    }
}
