<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Vending;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/** Runs the real entry points as separate processes, sharing one SQLite file (ADR-003). */
final class CliTest extends TestCase
{
    private string $database;

    protected function setUp(): void
    {
        $this->database = sys_get_temp_dir() . '/vending-' . uniqid() . '.sqlite';
    }

    protected function tearDown(): void
    {
        if (is_file($this->database)) {
            unlink($this->database);
        }
    }

    #[Test]
    public function it_runs_the_spec_examples(): void
    {
        $this->execute('service', "SERVICE, WATER=0.65x10, SODA=1.50x8, 0.05x20, 0.10x20, 0.25x20\n");

        $output = $this->execute('vending', "1, 0.25, 0.25, GET-SODA\n0.10, 0.10, RETURN-COIN\n1, GET-WATER\n");

        self::assertSame("-> SODA\n-> 0.10, 0.10\n-> WATER, 0.25, 0.10\n", $output);
    }

    #[Test]
    public function it_keeps_the_state_between_the_two_entry_points(): void
    {
        $this->execute('service', "SERVICE, WATER=0.65x10, 0.05x20, 0.10x20, 0.25x20\n");
        $this->execute('vending', "1, GET-WATER\n");

        $output = $this->execute('service', "STATUS\n");

        self::assertSame("-> WATER=0.65x9, BALANCE 0, 0.05x20, 0.10x19, 0.25x19, CASH BOX 1x1\n", $output);
    }

    #[Test]
    public function it_stops_reading_on_exit(): void
    {
        $output = $this->execute('vending', "0.10, RETURN-COIN\nEXIT\n0.25, RETURN-COIN\n");

        self::assertSame("-> 0.10\n", $output);
    }

    private function execute(string $script, string $input): string
    {
        $process = proc_open(
            [PHP_BINARY, dirname(__DIR__, 3) . '/bin/' . $script],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            null,
            ['VENDING_DB' => $this->database],
        );
        self::assertIsResource($process);

        fwrite($pipes[0], $input);
        fclose($pipes[0]);
        $output = (string) stream_get_contents($pipes[1]);
        $errors = (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        self::assertSame(0, proc_close($process), $errors);

        return $output;
    }
}
