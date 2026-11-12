<?php

declare(strict_types=1);

namespace App\Tests\Application\Vending;

use App\Vending\Application\InsertCoin;
use App\Vending\Application\InsertCoinHandler;
use App\Vending\Application\ReturnCoin;
use App\Vending\Application\ReturnCoinHandler;
use App\Vending\Domain\Coin;
use App\Vending\Domain\CoinsReturned;
use App\Vending\Domain\Money;
use App\Vending\Infrastructure\InMemoryVendingMachineRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ReturnCoinHandlerTest extends TestCase
{
    #[Test]
    public function it_returns_the_balance_and_saves_the_machine(): void
    {
        $repository = new InMemoryVendingMachineRepository();
        $insertCoin = new InsertCoinHandler($repository);
        $insertCoin(new InsertCoin(Money::fromCents(10)));
        $insertCoin(new InsertCoin(Money::fromCents(10)));
        $handler = new ReturnCoinHandler($repository);

        $events = $handler(new ReturnCoin());

        self::assertEquals([new CoinsReturned([Coin::TenCents, Coin::TenCents])], $events);
        self::assertTrue($repository->get()->balance()->isZero());
    }
}
