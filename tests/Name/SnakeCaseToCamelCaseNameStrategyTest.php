<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator\Tests\Name;

use Kenny1911\DoctrineDbalHydrator\Name\SnakeCaseToCamelCaseNameStrategy;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @psalm-internal Kenny1911\DoctrineDbalHydrator\Tests\Name
 */
final class SnakeCaseToCamelCaseNameStrategyTest extends TestCase
{
    /**
     * @param non-empty-string $name
     * @param non-empty-string $expected
     */
    #[TestWith(['foo', 'foo'])]
    #[TestWith(['foo_bar', 'fooBar'])]
    #[TestWith(['foo_bar', 'fooBar'])]
    #[TestWith(['fooBar', 'fooBar'])]
    #[TestWith(['Foo_Bar', 'fooBar'])]
    #[TestWith(['Foo_bar', 'fooBar'])]
    public function test(string $name, string $expected): void
    {
        $nameStrategy = new SnakeCaseToCamelCaseNameStrategy();

        self::assertSame($expected, $nameStrategy->convert($name));
    }
}
