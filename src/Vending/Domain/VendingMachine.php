<?php

declare(strict_types=1);

namespace App\Vending\Domain;

/** Aggregate root: Items, Balance and coins change together, so their rules are enforced here. */
final class VendingMachine
{
    private Money $balance;

    public function __construct()
    {
        $this->balance = Money::fromCents(0);
    }

    public function insertCoin(Coin $coin): void
    {
        $this->balance = $this->balance->add($coin->money());
    }

    public function balance(): Money
    {
        return $this->balance;
    }
}
