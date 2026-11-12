<?php

declare(strict_types=1);

namespace App\Vending\Infrastructure\Cli;

use App\Vending\Application\ItemStatus;
use App\Vending\Application\MachineStatus;
use App\Vending\Domain\Coin;
use App\Vending\Domain\Coins;

/** Renders the Machine Status in the SERVICE notation, so operators read back what they typed (D-010). */
final class StatusText
{
    /** @return list<string> */
    public static function forCustomer(MachineStatus $status): array
    {
        $texts = array_map(
            static fn(ItemStatus $item): string => sprintf('%s=%sx%d', $item->selector->value, MoneyText::format($item->price), $item->stock),
            $status->items,
        );
        $texts[] = 'BALANCE ' . MoneyText::format($status->balance);

        return $texts;
    }

    /**
     * Adds the Available Change and the Cash Box: coin levels and collected money are operator information (D-011).
     *
     * @return list<string>
     */
    public static function forOperator(MachineStatus $status): array
    {
        $cashBox = self::coinCounts($status->cashBox);

        return [
            ...self::forCustomer($status),
            ...self::coinCounts($status->availableChange),
            'CASH BOX ' . ($cashBox === [] ? '0' : implode(' ', $cashBox)),
        ];
    }

    /** @return list<string> e.g. 0.05x20, only coins the collection holds */
    private static function coinCounts(Coins $coins): array
    {
        $texts = [];

        foreach (Coin::cases() as $coin) {
            $count = $coins->count($coin);

            if ($count > 0) {
                $texts[] = sprintf('%sx%d', MoneyText::format($coin->money()), $count);
            }
        }

        return $texts;
    }
}
