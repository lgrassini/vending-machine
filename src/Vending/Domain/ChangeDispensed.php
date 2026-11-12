<?php

declare(strict_types=1);

namespace App\Vending\Domain;

/** The machine gave the Customer their Change after a sale. */
final readonly class ChangeDispensed implements DomainEvent
{
    /** @param list<Coin> $coins */
    public function __construct(public array $coins) {}
}
