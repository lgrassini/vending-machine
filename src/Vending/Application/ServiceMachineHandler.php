<?php

declare(strict_types=1);

namespace App\Vending\Application;

use App\Vending\Domain\DomainEvent;
use App\Vending\Domain\VendingMachineRepository;

/** Use case Service Machine: loads the machine, runs the command, saves it. */
final readonly class ServiceMachineHandler
{
    public function __construct(private VendingMachineRepository $machines) {}

    /** @return list<DomainEvent> */
    public function __invoke(ServiceMachine $command): array
    {
        $machine = $this->machines->get();
        $machine->service($command->availableChange, ...$command->items);
        $this->machines->save($machine);

        return $machine->releaseEvents();
    }
}
