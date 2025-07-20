<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator\Tests\Name;

use Kenny1911\DoctrineDbalHydrator\Name\SameNameStrategy;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @psalm-internal Kenny1911\DoctrineDbalHydrator\Tests\Name
 */
final class SameNameStrategyTest extends TestCase
{
    /**
     * @param non-empty-string $name
     */
    #[TestWith(['foo'])]
    #[TestWith(['fooBar'])]
    #[TestWith(['foo_bar'])]
    #[TestWith(['FooBar'])]
    #[TestWith(['FOO_BAR'])]
    public function test(string $name): void
    {
        $nameStrategy = new SameNameStrategy();

        self::assertSame($name, $nameStrategy->convert($name));
    }
}
