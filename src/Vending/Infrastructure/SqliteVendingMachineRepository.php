<?php

declare(strict_types=1);

namespace App\Vending\Infrastructure;

use App\Vending\Domain\Coin;
use App\Vending\Domain\Coins;
use App\Vending\Domain\Item;
use App\Vending\Domain\Money;
use App\Vending\Domain\Selector;
use App\Vending\Domain\VendingMachine;
use App\Vending\Domain\VendingMachineRepository;

/** Keeps the machine in a SQLite file, so state survives restarts (ADR-001). */
final class SqliteVendingMachineRepository implements VendingMachineRepository
{
    private const string AVAILABLE_CHANGE = 'available_change';
    private const string CASH_BOX = 'cash_box';

    private \PDO $pdo;

    public function __construct(string $path)
    {
        $this->pdo = new \PDO('sqlite:' . $path, options: [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS machine (id INTEGER PRIMARY KEY CHECK (id = 1), balance_cents INTEGER NOT NULL)');
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS items (selector TEXT PRIMARY KEY, price_cents INTEGER NOT NULL, stock INTEGER NOT NULL)');
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS coins (container TEXT NOT NULL, coin_cents INTEGER NOT NULL, count INTEGER NOT NULL, PRIMARY KEY (container, coin_cents))');
    }

    public function get(): VendingMachine
    {
        /** @var list<array{balance_cents: int}> $machine */
        $machine = $this->rows('SELECT balance_cents FROM machine');

        // Nothing saved yet: a new machine starts empty until the first SERVICE.
        if ($machine === []) {
            return new VendingMachine(Coins::empty());
        }

        /** @var list<array{selector: string, price_cents: int, stock: int}> $itemRows */
        $itemRows = $this->rows('SELECT selector, price_cents, stock FROM items');
        $items = array_map(
            static fn(array $row): Item => new Item(new Selector($row['selector']), Money::fromCents($row['price_cents']), $row['stock']),
            $itemRows,
        );

        return VendingMachine::reconstitute(
            Money::fromCents($machine[0]['balance_cents']),
            $this->coins(self::AVAILABLE_CHANGE),
            $this->coins(self::CASH_BOX),
            ...$items,
        );
    }

    public function save(VendingMachine $machine): void
    {
        $this->pdo->beginTransaction();

        try {
            $this->pdo->exec('DELETE FROM machine');
            $this->pdo->exec('DELETE FROM items');
            $this->pdo->exec('DELETE FROM coins');

            $this->execute('INSERT INTO machine (id, balance_cents) VALUES (1, ?)', [$machine->balance()->cents()]);

            foreach ($machine->items() as $item) {
                $this->execute(
                    'INSERT INTO items (selector, price_cents, stock) VALUES (?, ?, ?)',
                    [$item->selector->value, $item->price->cents(), $item->stock],
                );
            }

            $this->saveCoins(self::AVAILABLE_CHANGE, $machine->availableChange());
            $this->saveCoins(self::CASH_BOX, $machine->cashBox());

            $this->pdo->commit();
        } catch (\Throwable $exception) {
            $this->pdo->rollBack();

            throw $exception;
        }
    }

    private function saveCoins(string $container, Coins $coins): void
    {
        foreach (Coin::cases() as $coin) {
            $this->execute(
                'INSERT INTO coins (container, coin_cents, count) VALUES (?, ?, ?)',
                [$container, $coin->value, $coins->count($coin)],
            );
        }
    }

    private function coins(string $container): Coins
    {
        /** @var list<array{coin_cents: int, count: int}> $rows */
        $rows = $this->rows('SELECT coin_cents, count FROM coins WHERE container = ?', [$container]);
        $coins = Coins::empty();

        foreach ($rows as $row) {
            for ($i = 0; $i < $row['count']; $i++) {
                $coins = $coins->add(Coin::from($row['coin_cents']));
            }
        }

        return $coins;
    }

    /**
     * @param list<int|string> $parameters
     * @return list<mixed>
     */
    private function rows(string $sql, array $parameters = []): array
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($parameters);

        return array_values($statement->fetchAll(\PDO::FETCH_ASSOC));
    }

    /** @param list<int|string> $parameters */
    private function execute(string $sql, array $parameters): void
    {
        $this->pdo->prepare($sql)->execute($parameters);
    }
}
