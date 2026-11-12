<?php

declare(strict_types=1);

namespace App\Tests\Unit\Vending\Domain;

use App\Vending\Domain\Item;
use App\Vending\Domain\Money;
use App\Vending\Domain\Selector;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ItemTest extends TestCase
{
    #[Test]
    public function it_never_has_negative_stock(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Item(new Selector('WATER'), Money::fromCents(65), -1);
    }

    #[Test]
    public function it_reduces_stock_when_dispensed(): void
    {
        $item = new Item(new Selector('WATER'), Money::fromCents(65), 2);

        $item->dispense();

        self::assertSame(1, $item->stock);
    }
}
