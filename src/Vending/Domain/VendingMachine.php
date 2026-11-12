<?php

declare(strict_types=1);

namespace App\Vending\Domain;

/** Aggregate root: Items, Balance and coins change together, so their rules are enforced here. */
final class VendingMachine
{
    private Money $balance;

    private Coins $cashBox;

    /** @var list<DomainEvent> */
    private array $events = [];

    public function __construct(private Coins $availableChange)
    {
        $this->balance = Money::fromCents(0);
        $this->cashBox = Coins::empty();
    }

    public function insertCoin(Coin $coin): void
    {
        $balance = $this->balance->add($coin->money());

        // A 1 coin is accepted only if the whole Balance could still be refunded (D-001).
        if (!$coin->isReturnable() && new ChangeCalculator()->calculate($balance, $this->availableChange) === null) {
            $this->events[] = new CoinRejected($coin, CoinRejectionReason::RefundNotGuaranteed);

            return;
        }

        $this->balance = $balance;

        // 1 coins are never given back, so they don't count as change (D-001).
        if ($coin->isReturnable()) {
            $this->availableChange = $this->availableChange->add($coin);
        } else {
            $this->cashBox = $this->cashBox->add($coin);
        }

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

    public function cashBox(): Coins
    {
        return $this->cashBox;
    }

    /** @return list<DomainEvent> */
    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }
}
