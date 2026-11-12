<?php

declare(strict_types=1);

namespace App\Vending\Application;

use App\Vending\Domain\DomainEvent;
use App\Vending\Domain\VendingMachineRepository;

/** Use case Select Item: loads the machine, runs the command, saves it. */
final readonly class SelectItemHandler
{
    public function __construct(private VendingMachineRepository $machines) {}

    /** @return list<DomainEvent> */
    public function __invoke(SelectItem $command): array
    {
        $machine = $this->machines->get();
        $machine->selectItem($command->selector);
        $this->machines->save($machine);

        return $machine->releaseEvents();
    }
}
