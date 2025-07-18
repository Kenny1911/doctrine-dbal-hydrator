<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator\Mapping;

use Kenny1911\DoctrineDbalHydrator\Type\EnumType;

/**
 * @api
 */
final class Property
{
    /**
     * @param non-empty-string $name
     * @param non-empty-string|EnumType|null $type
     * @param non-empty-string|null $columnName
     */
    public function __construct(
        public readonly string $name,
        public readonly null|string|EnumType $type = null,
        public readonly ?string $columnName = null,
    ) {}
}
