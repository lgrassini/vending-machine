<?php

declare(strict_types=1);

namespace App\Vending\Infrastructure\Cli;

use App\Vending\Application\GetMachineStatus;
use App\Vending\Application\GetMachineStatusHandler;
use App\Vending\Application\ServiceMachine;
use App\Vending\Application\ServiceMachineHandler;
use App\Vending\Domain\Coin;
use App\Vending\Domain\Coins;
use App\Vending\Domain\Item;
use App\Vending\Domain\Selector;

/** The machine's service panel, behind the locked door: runs SERVICE and STATUS (ADR-003, D-010). */
final readonly class ServiceConsole
{
    public function __construct(
        private ServiceMachineHandler $serviceMachine,
        private GetMachineStatusHandler $getStatus,
    ) {}

    public function handle(string $line): string
    {
        $parts = array_values(array_filter(
            array_map(trim(...), explode(',', $line)),
            static fn(string $part): bool => $part !== '',
        ));

        if ($parts === []) {
            return '';
        }

        $outputs = match (true) {
            $parts === ['STATUS'] => StatusText::forOperator(($this->getStatus)(new GetMachineStatus())),
            $parts[0] === 'SERVICE' => $this->service(array_slice($parts, 1)),
            default => ['UNKNOWN ACTION: ' . implode(', ', $parts)],
        };

        return '-> ' . implode(', ', $outputs);
    }

    /**
     * All or nothing: one invalid part and the machine is not serviced.
     *
     * @param list<string> $parts e.g. WATER=0.65x10 (item) or 0.05x20 (change)
     * @return list<string>
     */
    private function service(array $parts): array
    {
        $items = [];
        $change = Coins::empty();

        foreach ($parts as $part) {
            $item = self::item($part);

            if ($item !== null) {
                $items[] = $item;

                continue;
            }

            $coins = self::changeCoins($part);

            if ($coins === null) {
                return ['INVALID SERVICE: ' . $part];
            }

            foreach ($coins as $coin) {
                $change = $change->add($coin);
            }
        }

        return EventText::render(($this->serviceMachine)(new ServiceMachine($change, ...$items)));
    }

    /** SELECTOR=PRICExSTOCK, e.g. WATER=0.65x10. */
    private static function item(string $part): ?Item
    {
        if (preg_match('/^([^=\s]+)=(\S+)x(\d+)$/', $part, $matches) !== 1) {
            return null;
        }

        $price = MoneyText::parse($matches[2]);

        return $price === null ? null : new Item(new Selector($matches[1]), $price, (int) $matches[3]);
    }

    /**
     * VALUExCOUNT, e.g. 0.05x20. Only coins that can be given as change (D-001).
     *
     * @return list<Coin>|null
     */
    private static function changeCoins(string $part): ?array
    {
        if (preg_match('/^(\S+)x(\d+)$/', $part, $matches) !== 1) {
            return null;
        }

        $value = MoneyText::parse($matches[1]);
        $coin = $value === null ? null : Coin::tryFromMoney($value);

        if ($coin === null || !$coin->isReturnable()) {
            return null;
        }

        return array_fill(0, (int) $matches[2], $coin);
    }
}
