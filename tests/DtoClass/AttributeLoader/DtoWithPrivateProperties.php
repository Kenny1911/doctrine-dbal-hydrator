<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\AttributeLoader;

use Doctrine\DBAL\Types\Types;
use Kenny1911\DoctrineDbalHydrator\Mapping\Attribute\Column;

/**
 * @internal
 * @psalm-internal Kenny1911\DoctrineDbalHydrator\Tests
 */
final class DtoWithPrivateProperties
{
    #[Column]
    private ?string $stringProperty = null;

    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $dateTimeProperty = null;

    #[Column(name: 'enum_property')]
    private ?DtoStringEnum $enumStringProperty = null;

    #[Column(type: Types::INTEGER)]
    private ?DtoIntEnum $enumIntProperty = null;

    public function getStringProperty(): ?string
    {
        return $this->stringProperty;
    }

    public function getDateTimeProperty(): ?\DateTimeImmutable
    {
        return $this->dateTimeProperty;
    }

    public function getEnumStringProperty(): ?DtoStringEnum
    {
        return $this->enumStringProperty;
    }

    public function getEnumIntProperty(): ?DtoIntEnum
    {
        return $this->enumIntProperty;
    }
}
