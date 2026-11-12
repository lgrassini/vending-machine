<?php

declare(strict_types=1);

namespace App\Vending\Domain;

/** A Customer inserted an accepted Coin. */
final readonly class CoinInserted implements DomainEvent
{
    public function __construct(public Coin $coin) {}
}
