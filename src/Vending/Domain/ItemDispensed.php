<?php

declare(strict_types=1);

namespace App\Vending\Domain;

/** The machine gave the selected Item to the Customer. */
final readonly class ItemDispensed implements DomainEvent
{
    public function __construct(public Selector $selector) {}
}
