<?php

declare(strict_types=1);

namespace App\Vending\Application;

use App\Vending\Domain\DomainEvent;
use App\Vending\Domain\VendingMachineRepository;

/** Use case Return Coin: loads the machine, runs the command, saves it. */
final readonly class ReturnCoinHandler
{
    public function __construct(private VendingMachineRepository $machines) {}

    /** @return list<DomainEvent> */
    public function __invoke(ReturnCoin $command): array
    {
        $machine = $this->machines->get();
        $machine->returnCoin();
        $this->machines->save($machine);

        return $machine->releaseEvents();
    }
}
