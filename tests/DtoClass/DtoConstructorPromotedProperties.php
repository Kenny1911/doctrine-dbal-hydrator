<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator\Tests\DtoClass;

/**
 * @internal
 * @psalm-internal Kenny1911\DoctrineDbalHydrator\Tests
 */
final class DtoConstructorPromotedProperties
{
    public function __construct(
        public readonly string $foo,
        public readonly int $bar = 0,
    ) {}
}
