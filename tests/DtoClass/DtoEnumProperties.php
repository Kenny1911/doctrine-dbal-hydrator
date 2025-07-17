<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator\Tests\DtoClass;

/**
 * @internal
 * @psalm-internal Kenny1911\DoctrineDbalHydrator\Tests
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class DtoEnumProperties
{
    public DtoEnumPropertiesEnum $property;

    public ?DtoEnumPropertiesEnum $propertyNullable;

    public function __construct(
        public DtoEnumPropertiesEnum $constructorArg,
        public ?DtoEnumPropertiesEnum $constructorArgNullable = null,
    ) {}
}
