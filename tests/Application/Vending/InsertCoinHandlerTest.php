<?php

declare(strict_types=1);

namespace App\Tests\Application\Vending;

use App\Vending\Application\InsertCoin;
use App\Vending\Application\InsertCoinHandler;
use App\Vending\Domain\Coin;
use App\Vending\Domain\CoinInserted;
use App\Vending\Domain\Money;
use App\Vending\Infrastructure\InMemoryVendingMachineRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class InsertCoinHandlerTest extends TestCase
{
    #[Test]
    public function it_inserts_a_coin_and_saves_the_machine(): void
    {
        $repository = new InMemoryVendingMachineRepository();
        $handler = new InsertCoinHandler($repository);

        $events = $handler(new InsertCoin(Money::fromCents(25)));

        self::assertEquals([new CoinInserted(Coin::TwentyFiveCents)], $events);
        self::assertEquals(Money::fromCents(25), $repository->get()->balance());
    }
}
