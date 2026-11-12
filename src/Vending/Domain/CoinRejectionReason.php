<?php

declare(strict_types=1);

namespace App\Vending\Domain;

/** Why the machine gave a Coin back instead of accepting it. */
enum CoinRejectionReason
{
    case InvalidCoin;
    case RefundNotGuaranteed;
}
