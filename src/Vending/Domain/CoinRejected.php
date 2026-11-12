<?php

declare(strict_types=1);

namespace App\Vending\Domain;

/** The machine refused the inserted money and gave it back (D-008). */
final readonly class CoinRejected implements DomainEvent
{
    public function __construct(
        public Money $inserted,
        public CoinRejectionReason $reason,
    ) {}
}
