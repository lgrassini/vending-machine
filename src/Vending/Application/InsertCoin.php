<?php

declare(strict_types=1);

namespace App\Vending\Application;

use App\Vending\Domain\Money;

/** A Customer inserts money into the machine. */
final readonly class InsertCoin
{
    public function __construct(public Money $inserted) {}
}
