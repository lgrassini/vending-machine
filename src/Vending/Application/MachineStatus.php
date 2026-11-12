<?php

declare(strict_types=1);

namespace App\Vending\Application;

use App\Vending\Domain\Coins;
use App\Vending\Domain\Money;

/** Read-only view of the machine: Items, Balance, Available Change and Cash Box. */
final readonly class MachineStatus
{
    /** @param list<ItemStatus> $items */
    public function __construct(
        public array $items,
        public Money $balance,
        public Coins $availableChange,
        public Coins $cashBox,
    ) {}
}
