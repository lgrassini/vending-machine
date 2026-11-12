<?php

declare(strict_types=1);

namespace App\Vending\Infrastructure;

use App\Vending\Domain\Coins;
use App\Vending\Domain\VendingMachine;
use App\Vending\Domain\VendingMachineRepository;

/** Keeps the machine in memory, so state is lost on restart. Used by tests. */
final class InMemoryVendingMachineRepository implements VendingMachineRepository
{
    private VendingMachine $machine;

    public function __construct()
    {
        $this->machine = new VendingMachine(Coins::empty());
    }

    public function get(): VendingMachine
    {
        return $this->machine;
    }

    public function save(VendingMachine $machine): void
    {
        $this->machine = $machine;
    }
}
