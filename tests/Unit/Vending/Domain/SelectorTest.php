<?php

declare(strict_types=1);

namespace App\Tests\Unit\Vending\Domain;

use App\Vending\Domain\Selector;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SelectorTest extends TestCase
{
    #[Test]
    public function it_is_never_empty(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Selector('');
    }
}
