<?php

declare(strict_types=1);

namespace App\Vending\Application;

use App\Vending\Domain\DomainEvent;
use App\Vending\Domain\VendingMachineRepository;

/** Use case Insert Coin: loads the machine, runs the command, saves it. */
final readonly class InsertCoinHandler
{
    public function __construct(private VendingMachineRepository $machines) {}

    /** @return list<DomainEvent> */
    public function __invoke(InsertCoin $command): array
    {
        $machine = $this->machines->get();
        $machine->insertCoin($command->inserted);
        $this->machines->save($machine);

        return $machine->releaseEvents();
    }
}
