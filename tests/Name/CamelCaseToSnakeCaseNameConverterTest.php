<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator\Tests\Name;

use Kenny1911\DoctrineDbalHydrator\Name\CamelCaseToSnakeCaseNameConverter;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @psalm-internal Kenny1911\DoctrineDbalHydrator\Tests\Name
 */
final class CamelCaseToSnakeCaseNameConverterTest extends TestCase
{
    /**
     * @param non-empty-string $name
     * @param non-empty-string $expected
     */
    #[TestWith(['foo', 'foo'])]
    #[TestWith(['Foo', 'foo'])]
    #[TestWith(['fooBar', 'foo_bar'])]
    #[TestWith(['FooBar', 'foo_bar'])]
    #[TestWith(['foo_bar', 'foo_bar'])]
    #[TestWith(['foo_Bar', 'foo_bar'])]
    #[TestWith(['Foo_Bar', 'foo_bar'])]
    public function test(string $name, string $expected): void
    {
        $nameStrategy = new CamelCaseToSnakeCaseNameConverter();

        self::assertSame($expected, $nameStrategy->convert($name));
    }
}
