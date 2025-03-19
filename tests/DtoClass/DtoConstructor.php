<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator\Tests\DtoClass;

/**
 * @internal
 * @psalm-internal Kenny1911\DoctrineDbalHydrator\Tests
 */
final class DtoConstructor
{
    public readonly string $foo;

    public readonly int $bar;

    public function __construct(
        string $foo,
        int $bar = 0,
    ) {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}
