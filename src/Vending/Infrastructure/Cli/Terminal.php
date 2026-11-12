<?php

declare(strict_types=1);

namespace App\Vending\Infrastructure\Cli;

/** Reads input line by line and prints each output, for both entry points. */
final class Terminal
{
    /** @param callable(string): string $handle a console's handle method */
    public static function run(callable $handle): void
    {
        // A person typing gets a prompt, line editing and history; piped input is read as is.
        $interactive = stream_isatty(STDIN);

        while (true) {
            $line = $interactive ? readline('> ') : fgets(STDIN);

            // EXIT is a terminal command, not a machine action, so it never reaches the console.
            if ($line === false || trim($line) === 'EXIT') {
                return;
            }

            if ($interactive && trim($line) !== '') {
                readline_add_history($line);
            }

            $output = $handle($line);

            if ($output !== '') {
                echo $output, PHP_EOL;
            }
        }
    }
}
