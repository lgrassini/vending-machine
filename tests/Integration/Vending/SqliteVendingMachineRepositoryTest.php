<?php

declare(strict_types=1);

namespace App\Tests\Integration\Vending;

use App\Vending\Domain\Coin;
use App\Vending\Domain\Coins;
use App\Vending\Domain\Item;
use App\Vending\Domain\Money;
use App\Vending\Domain\Selector;
use App\Vending\Infrastructure\SqliteVendingMachineRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SqliteVendingMachineRepositoryTest extends TestCase
{
    private string $path;

    protected function setUp(): void
    {
        $this->path = sys_get_temp_dir() . '/vending-' . uniqid() . '.sqlite';
    }

    protected function tearDown(): void
    {
        if (is_file($this->path)) {
            unlink($this->path);
        }
    }

    #[Test]
    public function it_keeps_the_machine_between_restarts(): void
    {
        $repository = new SqliteVendingMachineRepository($this->path);
        $machine = $repository->get();
        $machine->service(
            Coins::of(Coin::TwentyFiveCents, Coin::TwentyFiveCents, Coin::TwentyFiveCents, Coin::TwentyFiveCents),
            new Item(new Selector('WATER'), Money::fromCents(65), 5),
        );
        $machine->insertCoin(Money::fromCents(100));
        $machine->insertCoin(Money::fromCents(10));
        $machine->releaseEvents();
        $repository->save($machine);

        $reloaded = new SqliteVendingMachineRepository($this->path)->get();

        self::assertEquals($machine, $reloaded);
    }
}
