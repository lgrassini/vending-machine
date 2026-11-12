<?php

declare(strict_types=1);

namespace App\Vending\Domain;

/** Aggregate root: Items, Balance and coins change together, so their rules are enforced here. */
final class VendingMachine
{
    private Money $balance;

    private Coins $availableChange;

    /** @var list<DomainEvent> */
    private array $events = [];

    public function __construct()
    {
        $this->balance = Money::fromCents(0);
        $this->availableChange = Coins::empty();
    }

    public function insertCoin(Coin $coin): void
    {
        $this->balance = $this->balance->add($coin->money());
        $this->availableChange = $this->availableChange->add($coin);
        $this->events[] = new CoinInserted($coin);
    }

    public function balance(): Money
    {
        return $this->balance;
    }

    public function availableChange(): Coins
    {
        return $this->availableChange;
    }

    /** @return list<DomainEvent> */
    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }
}
