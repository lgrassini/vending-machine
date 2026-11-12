<?php

declare(strict_types=1);

namespace App\Vending\Infrastructure\Cli;

use App\Vending\Application\GetMachineStatus;
use App\Vending\Application\GetMachineStatusHandler;
use App\Vending\Application\InsertCoin;
use App\Vending\Application\InsertCoinHandler;
use App\Vending\Application\ReturnCoin;
use App\Vending\Application\ReturnCoinHandler;
use App\Vending\Application\SelectItem;
use App\Vending\Application\SelectItemHandler;
use App\Vending\Domain\Selector;

/** The machine's front panel: runs a line of customer actions and returns the output line (ADR-003, D-010). */
final readonly class CustomerConsole
{
    public function __construct(
        private InsertCoinHandler $insertCoin,
        private SelectItemHandler $selectItem,
        private ReturnCoinHandler $returnCoin,
        private GetMachineStatusHandler $getStatus,
    ) {}

    /** Returns an empty string when nothing comes out, e.g. after inserting money. */
    public function handle(string $line): string
    {
        $outputs = [];

        foreach (explode(',', $line) as $action) {
            $action = trim($action);

            if ($action !== '') {
                array_push($outputs, ...$this->run($action));
            }
        }

        return $outputs === [] ? '' : '-> ' . implode(', ', $outputs);
    }

    /** @return list<string> */
    private function run(string $action): array
    {
        $money = MoneyText::parse($action);

        if ($money !== null) {
            return EventText::render(($this->insertCoin)(new InsertCoin($money)));
        }

        if (preg_match('/^GET-(.+)$/', $action, $matches) === 1) {
            return EventText::render(($this->selectItem)(new SelectItem(new Selector($matches[1]))));
        }

        return match ($action) {
            'RETURN-COIN' => EventText::render(($this->returnCoin)(new ReturnCoin())),
            'STATUS' => StatusText::forCustomer(($this->getStatus)(new GetMachineStatus())),
            default => ['UNKNOWN ACTION: ' . $action],
        };
    }
}
