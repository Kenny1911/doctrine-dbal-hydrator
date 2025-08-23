<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator\Tests;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Types\Types;
use Kenny1911\DoctrineDbalHydrator\HydratorException;
use Kenny1911\DoctrineDbalHydrator\ObjectHydrator;
use Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\DtoConstructor;
use Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\DtoConstructorAndProperties;
use Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\DtoConstructorPromotedProperties;
use Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\DtoEnumProperties;
use Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\DtoEnumPropertiesEnum;
use Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\DtoOnlyProperties;
use Kenny1911\DoctrineDbalHydrator\Type\EnumType;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @psalm-internal Kenny1911\DoctrineDbalHydrator\Tests
 */
final class ObjectHydratorTest extends TestCase
{
    private ObjectHydrator $hydrator;

    #[\Override]
    protected function setUp(): void
    {
        $this->hydrator = new ObjectHydrator(new MySQLPlatform());
    }

    /**
     * @param array<non-empty-string, mixed> $data
     * @param array<non-empty-string, non-empty-string> $types
     * @param array<non-empty-string, non-empty-string> $mapping
     */
    #[TestWith(['Foo', 0, ['foo' => 'Foo'], [], []])]
    #[TestWith(['Foo', 123, ['foo' => 'Foo', 'bar' => 123], [], []])]
    #[TestWith(['Foo', 123, ['foo' => 'Foo', 'bar' => '123'], ['bar' => 'integer'], []])]
    #[TestWith(['Foo', 123, ['foo' => 'Foo', 'bar' => '123'], ['foo' => 'text', 'bar' => 'integer'], []])]
    #[TestWith(['Foo', 123, ['foo_column' => 'Foo', 'bar_column' => '123'], ['foo' => 'text', 'bar' => 'integer'], ['foo_column' => 'foo', 'bar_column' => 'bar']])]
    #[TestWith(['Foo', 123, ['foo' => 'Foo', 'bar' => 123, 'baz' => 'Not existing property'], [], []])]
    public function testHydrateDtoConstructor(string $expectedFoo, int $expectedBar, array $data, array $types, array $mapping): void
    {
        $object = $this->hydrator->hydrate(DtoConstructor::class, $data, $types, $mapping);

        self::assertInstanceOf(DtoConstructor::class, $object);
        self::assertSame($expectedFoo, $object->foo);
        self::assertSame($expectedBar, $object->bar);
    }

    /**
     * @param array<non-empty-string, mixed> $data
     * @param array<non-empty-string, non-empty-string> $types
     * @param array<non-empty-string, non-empty-string> $mapping
     */
    #[TestWith([
        'Foo',
        123,
        new \DateTimeImmutable('2025-01-01 00:00:00'),
        ['foo' => 'Foo', 'bar' => 123, 'baz' => new \DateTimeImmutable('2025-01-01 00:00:00')],
        ['baz' => 'datetime_immutable'],
        [],
    ])]
    #[TestWith(['Foo', 0, null, ['foo' => 'Foo'], [], []])]
    #[TestWith(['Foo', 0, null, ['foo' => 'Foo', 'qux' => 'Not existing property'], [], []])]
    #[TestWith([
        'Foo',
        123,
        new \DateTimeImmutable('2025-01-01 00:00:00'),
        ['foo_column' => 'Foo', 'bar_column' => 123, 'baz_column' => new \DateTimeImmutable('2025-01-01 00:00:00')],
        ['baz' => 'datetime_immutable'],
        ['foo_column' => 'foo', 'bar_column' => 'bar', 'baz_column' => 'baz'],
    ])]
    public function testHydrateDtoConstructorAndProperties(
        string $expectedFoo,
        int $expectedBar,
        ?\DateTimeImmutable $expectedBaz,
        array $data,
        array $types,
        array $mapping,
    ): void {
        $object = $this->hydrator->hydrate(DtoConstructorAndProperties::class, $data, $types, $mapping);

        self::assertInstanceOf(DtoConstructorAndProperties::class, $object);
        self::assertSame($expectedFoo, $object->foo);
        self::assertSame($expectedBar, $object->bar);
        self::assertEquals($expectedBaz, $object->baz);
    }

    /**
     * @param array<non-empty-string, mixed> $data
     * @param array<non-empty-string, non-empty-string> $types
     * @param array<non-empty-string, non-empty-string> $mapping
     */
    #[TestWith(['Foo', 0, ['foo' => 'Foo'], [], []])]
    #[TestWith(['Foo', 123, ['foo' => 'Foo', 'bar' => 123], [], []])]
    #[TestWith(['Foo', 123, ['foo' => 'Foo', 'bar' => '123'], ['bar' => 'integer'], []])]
    #[TestWith(['Foo', 123, ['foo' => 'Foo', 'bar' => '123'], ['foo' => 'text', 'bar' => 'integer'], []])]
    #[TestWith(['Foo', 123, ['foo_column' => 'Foo', 'bar_column' => '123'], ['foo' => 'text', 'bar' => 'integer'], ['foo_column' => 'foo', 'bar_column' => 'bar']])]
    #[TestWith(['Foo', 0, ['foo' => 'Foo', 'qux' => 'Not existing property'], [], []])]
    public function testHydrateDtoConstructorPromotedProperties(
        string $expectedFoo,
        int $expectedBar,
        array $data,
        array $types,
        array $mapping,
    ): void {
        $object = $this->hydrator->hydrate(DtoConstructorPromotedProperties::class, $data, $types, $mapping);

        self::assertInstanceOf(DtoConstructorPromotedProperties::class, $object);
        self::assertSame($expectedFoo, $object->foo);
        self::assertSame($expectedBar, $object->bar);
    }

    /**
     * @param array<non-empty-string, mixed> $data
     * @param array<non-empty-string, non-empty-string> $types
     * @param array<non-empty-string, non-empty-string> $mapping
     */
    #[TestWith([null, null, [], [], []])]
    #[TestWith(['Foo', null, ['foo' => 'Foo'], [], []])]
    #[TestWith(['Foo', 123, ['foo' => 'Foo', 'bar' => 123], [], []])]
    #[TestWith(['Foo', 123, ['foo' => 'Foo', 'bar' => '123'], ['bar' => 'integer'], []])]
    #[TestWith(['Foo', 123, ['foo' => 'Foo', 'bar' => '123'], ['foo' => 'text', 'bar' => 'integer'], []])]
    #[TestWith(['Foo', 123, ['foo_column' => 'Foo', 'bar_column' => '123'], ['foo' => 'text', 'bar' => 'integer'], ['foo_column' => 'foo', 'bar_column' => 'bar']])]
    #[TestWith([null, null, ['qux' => 'Not existing property'], [], []])]
    public function testHydrateDtoOnlyProperties(
        ?string $expectedFoo,
        ?int $expectedBar,
        array $data,
        array $types,
        array $mapping,
    ): void {
        $object = $this->hydrator->hydrate(DtoOnlyProperties::class, $data, $types, $mapping);

        self::assertInstanceOf(DtoOnlyProperties::class, $object);
        self::assertSame($expectedFoo, $object->foo);
        self::assertSame($expectedBar, $object->bar);
    }

    /**
     * @param array<non-empty-string, mixed> $data
     * @param array<non-empty-string, non-empty-string> $mapping
     */
    #[TestWith([
        DtoEnumPropertiesEnum::FOO,
        DtoEnumPropertiesEnum::BAR,
        DtoEnumPropertiesEnum::BAZ,
        DtoEnumPropertiesEnum::QUX,
        ['constructorArg' => 'foo', 'constructorArgNullable' => 'bar', 'property' => 'baz', 'propertyNullable' => 'qux'],
        [],
    ])]
    #[TestWith([
        DtoEnumPropertiesEnum::FOO,
        null,
        DtoEnumPropertiesEnum::BAZ,
        null,
        ['constructorArg' => 'foo', 'constructorArgNullable' => null, 'property' => 'baz', 'propertyNullable' => null],
        [],
    ])]
    #[TestWith([
        DtoEnumPropertiesEnum::FOO,
        DtoEnumPropertiesEnum::BAR,
        DtoEnumPropertiesEnum::BAZ,
        DtoEnumPropertiesEnum::QUX,
        ['constructor_arg' => 'foo', 'constructor_arg_nullable' => 'bar', 'property' => 'baz', 'property_nullable' => 'qux'],
        ['constructor_arg' => 'constructorArg', 'constructor_arg_nullable' => 'constructorArgNullable', 'property_nullable' => 'propertyNullable'],
    ])]
    public function testHydrateDtoEnumProperty(
        DtoEnumPropertiesEnum $expectedConstructorArg,
        ?DtoEnumPropertiesEnum $expectedConstructorArgNullable,
        DtoEnumPropertiesEnum $expectedProperty,
        ?DtoEnumPropertiesEnum $expectedPropertyNullable,
        array $data,
        array $mapping,
    ): void {
        $object = $this->hydrator->hydrate(
            DtoEnumProperties::class,
            $data,
            [
                'constructorArg' => new EnumType(DtoEnumPropertiesEnum::class, Types::STRING),
                'constructorArgNullable' => new EnumType(DtoEnumPropertiesEnum::class, Types::STRING),
                'property' => new EnumType(DtoEnumPropertiesEnum::class, Types::STRING),
                'propertyNullable' => new EnumType(DtoEnumPropertiesEnum::class, Types::STRING),
            ],
            $mapping,
        );

        self::assertInstanceOf(DtoEnumProperties::class, $object);
        self::assertSame($expectedConstructorArg, $object->constructorArg);
        self::assertSame($expectedConstructorArgNullable, $object->constructorArgNullable);
        self::assertSame($expectedProperty, $object->property);
        self::assertSame($expectedPropertyNullable, $object->propertyNullable);
    }

    /**
     * @param array<non-empty-string, mixed> $data
     *
     * @throws \Throwable
     */
    #[TestWith([['constructorArg' => 1, 'constructorArgNullable' => 'bar', 'property' => 'baz', 'propertyNullable' => 'qux']])]
    #[TestWith([['constructorArg' => 'foo', 'constructorArgNullable' => 1, 'property' => 'baz', 'propertyNullable' => 'qux']])]
    #[TestWith([['constructorArg' => 'foo', 'constructorArgNullable' => 'bar', 'property' => 1, 'propertyNullable' => 'qux']])]
    #[TestWith([['constructorArg' => 'foo', 'constructorArgNullable' => 'bar', 'property' => 'baz', 'propertyNullable' => 1]])]
    #[TestWith([['constructorArg' => null, 'constructorArgNullable' => 'bar', 'property' => 'baz', 'propertyNullable' => 'qux']])]
    #[TestWith([['constructorArg' => 'foo', 'constructorArgNullable' => 'bar', 'property' => null, 'propertyNullable' => 'qux']])]
    public function testHydrateDtoEnumPropertyInvalidEnumTypeError(
        array $data,
    ): void {
        self::expectException(HydratorException::class);

        try {
            $this->hydrator->hydrate(
                DtoEnumProperties::class,
                $data,
                [
                    'constructorArg' => new EnumType(DtoEnumPropertiesEnum::class, Types::STRING),
                    'constructorArgNullable' => new EnumType(DtoEnumPropertiesEnum::class, Types::STRING),
                    'property' => new EnumType(DtoEnumPropertiesEnum::class, Types::STRING),
                    'propertyNullable' => new EnumType(DtoEnumPropertiesEnum::class, Types::STRING),
                ],
            );
        } catch (\Throwable $e) {
            self::assertInstanceOf(\TypeError::class, $e->getPrevious());

            throw $e;
        }
    }

    /**
     * @param array<non-empty-string, mixed> $data
     *
     * @throws \Throwable
     */
    #[TestWith([['constructorArg' => 'invalid', 'constructorArgNullable' => 'bar', 'property' => 'baz', 'propertyNullable' => 'qux']])]
    #[TestWith([['constructorArg' => 'foo', 'constructorArgNullable' => 'invalid', 'property' => 'baz', 'propertyNullable' => 'qux']])]
    #[TestWith([['constructorArg' => 'foo', 'constructorArgNullable' => 'bar', 'property' => 'invalid', 'propertyNullable' => 'qux']])]
    #[TestWith([['constructorArg' => 'foo', 'constructorArgNullable' => 'bar', 'property' => 'naz', 'propertyNullable' => 'invalid']])]
    public function testHydrateDtoEnumPropertyValueError(
        array $data,
    ): void {
        self::expectException(HydratorException::class);

        try {
            $this->hydrator->hydrate(
                DtoEnumProperties::class,
                $data,
                [
                    'constructorArg' => new EnumType(DtoEnumPropertiesEnum::class, Types::STRING),
                    'constructorArgNullable' => new EnumType(DtoEnumPropertiesEnum::class, Types::STRING),
                    'property' => new EnumType(DtoEnumPropertiesEnum::class, Types::STRING),
                    'propertyNullable' => new EnumType(DtoEnumPropertiesEnum::class, Types::STRING),
                ],
            );
        } catch (\Throwable $e) {
            self::assertInstanceOf(\ValueError::class, $e->getPrevious());

            throw $e;
        }
    }
}
