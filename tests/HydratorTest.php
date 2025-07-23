<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator\Tests;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Kenny1911\DoctrineDbalHydrator\Hydrator;
use Kenny1911\DoctrineDbalHydrator\Mapping\AttributeLoader;
use Kenny1911\DoctrineDbalHydrator\ObjectHydrator;
use Kenny1911\DoctrineDbalHydrator\SimpleInstantinator;
use Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\AttributeLoader\Dto;
use Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\AttributeLoader\DtoIntEnum;
use Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\AttributeLoader\DtoStringEnum;
use Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\AttributeLoader\DtoWithPrivateProperties;
use Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\AttributeLoader\DtoWithPromotedProperties;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @psalm-internal Kenny1911\DoctrineDbalHydrator\Tests
 */
final class HydratorTest extends TestCase
{
    /**
     * @param class-string<Dto|DtoWithPrivateProperties|DtoWithPromotedProperties> $class
     */
    #[TestWith([Dto::class])]
    #[TestWith([DtoWithPrivateProperties::class])]
    #[TestWith([DtoWithPromotedProperties::class])]
    public function testHydrateUsingAttributeMapping(string $class): void
    {
        $hydrator = new Hydrator(
            objectHydrator: new ObjectHydrator(
                platform: new MySQLPlatform(),
                instantinator: new SimpleInstantinator(),
            ),
            loader: new AttributeLoader(),
        );

        $data = [
            'string_property' => 'STRING',
            'date_time_property' => '2025-01-01 13:45:32',
            'enum_property' => 'foo',
            'enum_int_property' => 2,
        ];

        $object = $hydrator->hydrate($class, $data);

        self::assertInstanceOf($class, $object);
        self::assertSame('STRING', self::getPropertyValue($object, 'stringProperty'));
        self::assertEquals(new \DateTimeImmutable('2025-01-01 13:45:32'), self::getPropertyValue($object, 'dateTimeProperty'));
        self::assertSame(DtoStringEnum::FOO, self::getPropertyValue($object, 'enumStringProperty'));
        self::assertSame(DtoIntEnum::TWO, self::getPropertyValue($object, 'enumIntProperty'));
    }

    /**
     * @param non-empty-string $property
     */
    private static function getPropertyValue(object $object, string $property): mixed
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return (new \ReflectionProperty($object::class, $property))->getValue($object);
    }
}
