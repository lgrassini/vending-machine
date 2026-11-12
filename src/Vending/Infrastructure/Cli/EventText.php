<?php

declare(strict_types=1);

namespace App\Vending\Infrastructure\Cli;

use App\Vending\Domain\ChangeDispensed;
use App\Vending\Domain\Coin;
use App\Vending\Domain\CoinInserted;
use App\Vending\Domain\CoinRejected;
use App\Vending\Domain\CoinRejectionReason;
use App\Vending\Domain\CoinsReturned;
use App\Vending\Domain\DomainEvent;
use App\Vending\Domain\ItemDispensed;
use App\Vending\Domain\MachineServiced;
use App\Vending\Domain\PurchaseRejected;
use App\Vending\Domain\PurchaseRejectionReason;

/** Renders domain events as the CLI output: what the machine hands out, plus the reason when it refuses (D-010). */
final class EventText
{
    /**
     * @param list<DomainEvent> $events
     * @return list<string>
     */
    public static function render(array $events): array
    {
        $texts = [];

        foreach ($events as $event) {
            array_push($texts, ...self::renderOne($event));
        }

        return $texts;
    }

    /** @return list<string> */
    private static function renderOne(DomainEvent $event): array
    {
        return match (true) {
            $event instanceof CoinInserted => [],
            $event instanceof CoinRejected => [MoneyText::format($event->inserted) . ' (' . self::coinReason($event->reason) . ')'],
            $event instanceof ItemDispensed => [$event->selector->value],
            $event instanceof ChangeDispensed, $event instanceof CoinsReturned => array_map(
                static fn(Coin $coin): string => MoneyText::format($coin->money()),
                $event->coins,
            ),
            $event instanceof PurchaseRejected => [self::purchaseReason($event->reason)],
            $event instanceof MachineServiced => ['SERVICED'],
            default => throw new \LogicException('No CLI text for ' . $event::class . '.'),
        };
    }

    private static function coinReason(CoinRejectionReason $reason): string
    {
        return match ($reason) {
            CoinRejectionReason::InvalidCoin => 'INVALID COIN',
            CoinRejectionReason::RefundNotGuaranteed => 'REFUND NOT GUARANTEED',
        };
    }

    private static function purchaseReason(PurchaseRejectionReason $reason): string
    {
        return match ($reason) {
            PurchaseRejectionReason::UnknownItem => 'UNKNOWN ITEM',
            PurchaseRejectionReason::OutOfStock => 'OUT OF STOCK',
            PurchaseRejectionReason::InsufficientFunds => 'INSUFFICIENT FUNDS',
            PurchaseRejectionReason::ChangeUnavailable => 'CHANGE UNAVAILABLE',
        };
    }
}
