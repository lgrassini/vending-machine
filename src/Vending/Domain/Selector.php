<?php

declare(strict_types=1);

namespace App\Vending\Domain;

/** The code a Customer uses to choose an Item (e.g. WATER). */
final readonly class Selector
{
    public function __construct(public string $value)
    {
        if ($value === '') {
            throw new \InvalidArgumentException("Selector can't be empty.");
        }
    }
}
