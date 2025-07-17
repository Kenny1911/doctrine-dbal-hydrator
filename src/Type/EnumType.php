<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator\Type;

/**
 * @api
 */
final class EnumType
{
    /**
     * @param class-string<\BackedEnum> $enum
     * @param non-empty-string $type
     */
    public function __construct(
        public readonly string $enum,
        public readonly string $type,
    ) {}
}
