<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\AttributeLoader;

use Doctrine\DBAL\Types\Types;
use Kenny1911\DoctrineDbalHydrator\Mapping\Attribute\Column;

/**
 * @internal
 * @psalm-internal Kenny1911\DoctrineDbalHydrator\Tests
 */
final class DtoWithPromotedProperties
{
    public function __construct(
        #[Column]
        public readonly string $stringProperty,
        #[Column(type: Types::DATETIME_IMMUTABLE)]
        public readonly \DateTimeImmutable $dateTimeProperty,
        #[Column(name: 'enum_property')]
        public readonly DtoStringEnum $enumStringProperty,
        #[Column(type: Types::INTEGER)]
        public readonly DtoIntEnum $enumIntProperty,
    ) {}
}
