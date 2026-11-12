<?php

declare(strict_types=1);

namespace App\Vending\Application;

use App\Vending\Domain\Coins;
use App\Vending\Domain\Item;

/** A Service Operator sets the Items and the Available Change. */
final readonly class ServiceMachine
{
    /** @var list<Item> */
    public array $items;

    public function __construct(public Coins $availableChange, Item ...$items)
    {
        $this->items = array_values($items);
    }
}
