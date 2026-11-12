<?php

declare(strict_types=1);

namespace App\Tests\Unit\Vending\Domain;

use App\Vending\Domain\Coin;
use App\Vending\Domain\Money;
use App\Vending\Domain\VendingMachine;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class VendingMachineTest extends TestCase
{
    #[Test]
    public function it_adds_an_inserted_coin_to_the_balance(): void
    {
        $machine = new VendingMachine();

        $machine->insertCoin(Coin::TwentyFiveCents);

        self::assertEquals(Money::fromCents(25), $machine->balance());
    }
}
