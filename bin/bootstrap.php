<?php

declare(strict_types=1);

use App\Vending\Infrastructure\SqliteVendingMachineRepository;

require __DIR__ . '/../vendor/autoload.php';

// Both entry points share the machine's state in one SQLite file (ADR-003).
$path = getenv('VENDING_DB') ?: __DIR__ . '/../var/vending.sqlite';

if (!is_dir(dirname($path))) {
    mkdir(dirname($path), 0o777, true);
}

return new SqliteVendingMachineRepository($path);
