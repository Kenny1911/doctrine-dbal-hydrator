<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator\Mapping;

/**
 * @api
 *
 * @template T of object
 */
final class Mapping
{
    /**
     * @param class-string<T> $class
     * @param list<Property> $properties
     */
    public function __construct(
        public readonly string $class,
        public readonly array $properties,
    ) {}
}
