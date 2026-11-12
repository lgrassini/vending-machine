<?php

declare(strict_types=1);

namespace App\Vending\Infrastructure\Cli;

use App\Vending\Application\ItemStatus;
use App\Vending\Application\MachineStatus;
use App\Vending\Domain\Coin;

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
     * Adds the Available Change: coin levels are operator information.
     *
     * @return list<string>
     */
    public static function forOperator(MachineStatus $status): array
    {
        $texts = self::forCustomer($status);

        foreach (Coin::cases() as $coin) {
            $count = $status->availableChange->count($coin);

            if ($count > 0) {
                $texts[] = sprintf('%sx%d', MoneyText::format($coin->money()), $count);
            }
        }

        return $texts;
    }
}
