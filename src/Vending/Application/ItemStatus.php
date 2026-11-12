<?php

declare(strict_types=1);

namespace App\Vending\Application;

use App\Vending\Domain\Money;
use App\Vending\Domain\Selector;

/** Read-only view of one Item in the Machine Status. */
final readonly class ItemStatus
{
    public function __construct(
        public Selector $selector,
        public Money $price,
        public int $stock,
    ) {}
}
