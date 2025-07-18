<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator\Tests\Mapping;

use Doctrine\DBAL\Types\Types;
use Kenny1911\DoctrineDbalHydrator\Mapping\AttributeLoader;
use Kenny1911\DoctrineDbalHydrator\Mapping\Property;
use Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\AttributeLoader\Dto;
use Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\AttributeLoader\DtoIntEnum;
use Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\AttributeLoader\DtoStringEnum;
use Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\AttributeLoader\DtoWithPrivateProperties;
use Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\AttributeLoader\DtoWithPromotedProperties;
use Kenny1911\DoctrineDbalHydrator\Type\EnumType;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @psalm-internal Kenny1911\DoctrineDbalHydrator\Tests\Mapping
 */
final class AttributeLoaderTest extends TestCase
{
    /**
     * @param class-string<Dto|DtoWithPrivateProperties|DtoWithPromotedProperties> $class
     *
     * @throws \ReflectionException
     */
    #[TestWith([Dto::class])]
    #[TestWith([DtoWithPrivateProperties::class])]
    #[TestWith([DtoWithPromotedProperties::class])]
    public function test(string $class): void
    {
        $loader = new AttributeLoader();
        $mapping = $loader->load($class);

        self::assertSame($class, $mapping->class);
        self::assertCount(4, $mapping->properties);
        self::assertProperty(
            new Property(
                name: 'stringProperty',
                type: Types::STRING,
                columnName: null,
            ),
            $mapping->properties[0],
        );
        self::assertProperty(
            new Property(
                name: 'dateTimeProperty',
                type: Types::DATETIME_IMMUTABLE,
                columnName: null,
            ),
            $mapping->properties[1],
        );
        self::assertProperty(
            new Property(
                name: 'enumStringProperty',
                type: new EnumType(enum: DtoStringEnum::class, type: Types::STRING),
                columnName: 'enum_property',
            ),
            $mapping->properties[2],
        );
        self::assertProperty(
            new Property(
                name: 'enumIntProperty',
                type: new EnumType(enum: DtoIntEnum::class, type: Types::INTEGER),
                columnName: null,
            ),
            $mapping->properties[3],
        );

        self::assertSame($mapping, $loader->load($class));
    }

    private static function assertProperty(Property $expected, Property $actual): void
    {
        self::assertEquals($expected, $actual);
    }
}
