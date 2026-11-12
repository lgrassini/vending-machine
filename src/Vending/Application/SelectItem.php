<?php

declare(strict_types=1);

namespace App\Vending\Application;

use App\Vending\Domain\Selector;

/** A Customer selects an Item to buy. */
final readonly class SelectItem
{
    public function __construct(public Selector $selector) {}
}
