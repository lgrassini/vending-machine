<?php

declare(strict_types=1);

namespace App\Vending\Application;

use App\Vending\Domain\Item;
use App\Vending\Domain\VendingMachineRepository;

/** Query Machine Status: reads the machine and saves nothing. */
final readonly class GetMachineStatusHandler
{
    public function __construct(private VendingMachineRepository $machines) {}

    public function __invoke(GetMachineStatus $query): MachineStatus
    {
        $machine = $this->machines->get();

        return new MachineStatus(
            array_map(
                static fn(Item $item): ItemStatus => new ItemStatus($item->selector, $item->price, $item->stock),
                $machine->items(),
            ),
            $machine->balance(),
            $machine->availableChange(),
        );
    }
}
