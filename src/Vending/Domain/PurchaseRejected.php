<?php

declare(strict_types=1);

namespace App\Vending\Domain;

/** The machine refused to sell the selected Item; the Balance is kept (D-008). */
final readonly class PurchaseRejected implements DomainEvent
{
    public function __construct(
        public Selector $selector,
        public PurchaseRejectionReason $reason,
    ) {}
}
