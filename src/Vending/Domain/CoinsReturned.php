<?php

declare(strict_types=1);

namespace App\Vending\Domain;

/** The machine gave the Balance back to the Customer. */
final readonly class CoinsReturned implements DomainEvent
{
    /** @param list<Coin> $coins */
    public function __construct(public array $coins) {}
}
