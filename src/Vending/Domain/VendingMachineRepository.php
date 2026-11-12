<?php

declare(strict_types=1);

namespace App\Vending\Domain;

/** Port to load and save the machine; each command is saved in one transaction (ADR-001). */
interface VendingMachineRepository
{
    public function get(): VendingMachine;

    public function save(VendingMachine $machine): void;
}
