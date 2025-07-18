<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\AttributeLoader;

use Doctrine\DBAL\Types\Types;
use Kenny1911\DoctrineDbalHydrator\Mapping\Attribute\Column;

/**
 * @internal
 * @psalm-internal Kenny1911\DoctrineDbalHydrator\Tests
 */
final class Dto
{
    #[Column]
    public ?string $stringProperty = null;

    #[Column(type: Types::DATETIME_IMMUTABLE)]
    public ?\DateTimeImmutable $dateTimeProperty = null;

    #[Column(name: 'enum_property')]
    public ?DtoStringEnum $enumStringProperty = null;

    #[Column(type: Types::INTEGER)]
    public ?DtoIntEnum $enumIntProperty = null;
}
