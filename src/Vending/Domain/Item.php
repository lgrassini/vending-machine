<?php

declare(strict_types=1);

namespace App\Vending\Domain;

/** A product for sale, identified by its Selector. An entity: its Stock changes over time. */
final class Item
{
    public function __construct(
        public readonly Selector $selector,
        public readonly Money $price,
        public private(set) int $stock,
    ) {
        if ($stock < 0) {
            throw new \InvalidArgumentException("Stock can't be negative, got {$stock}.");
        }
    }

    public function dispense(): void
    {
        $this->stock--;
    }
}
