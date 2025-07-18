<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator\Mapping\Attribute;

use Doctrine\DBAL\Types\Types;

/**
 * @api
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class Column
{
    /**
     * @param non-empty-string $type
     * @param non-empty-string|null $name
     */
    public function __construct(
        public readonly string $type = Types::STRING,
        public readonly ?string $name = null,
    ) {}
}
