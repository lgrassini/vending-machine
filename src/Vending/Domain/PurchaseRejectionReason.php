<?php

declare(strict_types=1);

namespace App\Vending\Domain;

/** Why the machine refused to sell the selected Item. */
enum PurchaseRejectionReason
{
    case UnknownItem;
    case OutOfStock;
    case InsufficientFunds;
}
