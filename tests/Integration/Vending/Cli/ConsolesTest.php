<?php

declare(strict_types=1);

namespace App\Tests\Integration\Vending\Cli;

use App\Vending\Application\GetMachineStatusHandler;
use App\Vending\Application\InsertCoinHandler;
use App\Vending\Application\ReturnCoinHandler;
use App\Vending\Application\SelectItemHandler;
use App\Vending\Application\ServiceMachineHandler;
use App\Vending\Infrastructure\Cli\CustomerConsole;
use App\Vending\Infrastructure\Cli\ServiceConsole;
use App\Vending\Infrastructure\InMemoryVendingMachineRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ConsolesTest extends TestCase
{
    private CustomerConsole $customer;

    private ServiceConsole $operator;

    protected function setUp(): void
    {
        $repository = new InMemoryVendingMachineRepository();
        $getStatus = new GetMachineStatusHandler($repository);
        $this->customer = new CustomerConsole(
            new InsertCoinHandler($repository),
            new SelectItemHandler($repository),
            new ReturnCoinHandler($repository),
            $getStatus,
        );
        $this->operator = new ServiceConsole(new ServiceMachineHandler($repository), $getStatus);
    }

    #[Test]
    public function it_buys_soda_with_exact_change(): void
    {
        $this->service();

        self::assertSame('-> SODA', $this->customer->handle('1, 0.25, 0.25, GET-SODA'));
    }

    #[Test]
    public function it_returns_the_coins_on_request(): void
    {
        self::assertSame('-> 0.10, 0.10', $this->customer->handle('0.10, 0.10, RETURN-COIN'));
    }

    #[Test]
    public function it_buys_water_without_exact_change(): void
    {
        $this->service();

        self::assertSame('-> WATER, 0.25, 0.10', $this->customer->handle('1, GET-WATER'));
    }

    #[Test]
    public function it_prints_nothing_when_nothing_comes_out(): void
    {
        self::assertSame('', $this->customer->handle('0.25'));
    }

    #[Test]
    public function it_tells_the_customer_why_it_refuses(): void
    {
        $this->service();

        self::assertSame('-> 0.01 (INVALID COIN)', $this->customer->handle('0.01'));
        self::assertSame('-> INSUFFICIENT FUNDS', $this->customer->handle('GET-WATER'));
        self::assertSame('-> UNKNOWN ITEM', $this->customer->handle('GET-COFFEE'));
        self::assertSame('-> UNKNOWN ACTION: SERVICE', $this->customer->handle('SERVICE'));
    }

    #[Test]
    public function it_shows_the_customer_status(): void
    {
        $this->service();

        self::assertSame('-> WATER=0.65x10, SODA=1.50x8, BALANCE 0.25', $this->customer->handle('0.25, STATUS'));
    }

    #[Test]
    public function it_shows_the_operator_status_with_the_available_change(): void
    {
        $this->service();

        self::assertSame(
            '-> WATER=0.65x10, SODA=1.50x8, BALANCE 0, 0.05x20, 0.10x20, 0.25x20, CASH BOX 0',
            $this->operator->handle('STATUS'),
        );
    }

    #[Test]
    public function it_services_nothing_when_a_part_is_invalid(): void
    {
        self::assertSame('-> INVALID SERVICE: 1x10', $this->operator->handle('SERVICE, WATER=0.65x10, 1x10'));
        self::assertSame('-> BALANCE 0, CASH BOX 0', $this->operator->handle('STATUS'));
    }

    #[Test]
    public function it_only_accepts_operator_actions(): void
    {
        self::assertSame('-> UNKNOWN ACTION: 0.25', $this->operator->handle('0.25'));
    }

    private function service(): void
    {
        self::assertSame(
            '-> SERVICED',
            $this->operator->handle('SERVICE, WATER=0.65x10, SODA=1.50x8, 0.05x20, 0.10x20, 0.25x20'),
        );
    }
}
