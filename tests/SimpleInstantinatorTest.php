<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator\Tests;

use Kenny1911\DoctrineDbalHydrator\HydratorException;
use Kenny1911\DoctrineDbalHydrator\SimpleInstantinator;
use Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\DtoConstructor;
use Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\DtoConstructorAndProperties;
use Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\DtoConstructorPromotedProperties;
use Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\DtoEnumProperties;
use Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\DtoEnumPropertiesEnum;
use Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\DtoOnlyProperties;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @psalm-internal Kenny1911\DoctrineDbalHydrator\Tests
 */
final class SimpleInstantinatorTest extends TestCase
{
    private SimpleInstantinator $instantinator;

    #[\Override]
    protected function setUp(): void
    {
        $this->instantinator = new SimpleInstantinator();
    }

    /**
     * @param array<non-empty-string, mixed> $data
     */
    #[TestWith(['Foo', 123, ['foo' => 'Foo', 'bar' => 123]])]
    #[TestWith([null, null, []])]
    #[TestWith(['Foo', null, ['foo' => 'Foo']])]
    public function testDtoOnlyProperties(?string $expectedFoo, ?int $expectedBar, array $data): void
    {
        $object = $this->instantinator->instantiate(DtoOnlyProperties::class, $data);

        self::assertInstanceOf(DtoOnlyProperties::class, $object);
        self::assertSame($expectedFoo, $object->foo);
        self::assertSame($expectedBar, $object->bar);
    }

    /**
     * @param array<non-empty-string, mixed> $data
     */
    #[TestWith(['Foo', 123, ['foo' => 'Foo', 'bar' => 123]])]
    #[TestWith(['Foo', 0, ['foo' => 'Foo']])]
    public function testDtoConstructor(string $expectedFoo, int $expectedBar, array $data): void
    {
        $object = $this->instantinator->instantiate(DtoConstructor::class, $data);

        self::assertInstanceOf(DtoConstructor::class, $object);
        self::assertSame($expectedFoo, $object->foo);
        self::assertSame($expectedBar, $object->bar);
    }

    public function testDtoConstructorThrowsNotSetRequiredConstructorArg(): void
    {
        $this->expectException(HydratorException::class);
        $this->expectExceptionMessage(\sprintf('Required constructor parameter "foo" of class %s is not defined.', DtoConstructor::class));

        $this->instantinator->instantiate(DtoConstructor::class, []);
    }

    /**
     * @param array<non-empty-string, mixed> $data
     */
    #[TestWith(['Foo', 123, ['foo' => 'Foo', 'bar' => 123]])]
    #[TestWith(['Foo', 0, ['foo' => 'Foo']])]
    public function testDtoConstructorPromotedProperties(string $expectedFoo, int $expectedBar, array $data): void
    {
        $object = $this->instantinator->instantiate(DtoConstructorPromotedProperties::class, $data);

        self::assertInstanceOf(DtoConstructorPromotedProperties::class, $object);
        self::assertSame($expectedFoo, $object->foo);
        self::assertSame($expectedBar, $object->bar);
    }

    public function testDtoConstructorPromotedPropertiesThrowsNotSetRequiredConstructorArg(): void
    {
        $this->expectException(HydratorException::class);
        $this->expectExceptionMessage(\sprintf('Required constructor parameter "foo" of class %s is not defined.', DtoConstructorPromotedProperties::class));

        $this->instantinator->instantiate(DtoConstructorPromotedProperties::class, []);
    }

    /**
     * @param array<non-empty-string, mixed> $data
     */
    #[TestWith(['Foo', 123, new \DateTimeImmutable('2025-01-01 00:00:00'), ['foo' => 'Foo', 'bar' => 123, 'baz' => new \DateTimeImmutable('2025-01-01 00:00:00')]])]
    #[TestWith(['Foo', 0, null, ['foo' => 'Foo', 'baz' => null]])]
    #[TestWith(['Foo', 0, null, ['foo' => 'Foo']])]
    public function testDtoConstructorAndProperties(
        string $expectedFoo,
        int $expectedBar,
        ?\DateTimeImmutable $expectedBaz,
        array $data,
    ): void {
        $object = $this->instantinator->instantiate(DtoConstructorAndProperties::class, $data);

        self::assertInstanceOf(DtoConstructorAndProperties::class, $object);
        self::assertSame($expectedFoo, $object->foo);
        self::assertSame($expectedBar, $object->bar);
        self::assertEquals($expectedBaz, $object->baz);
    }

    /**
     * @param array<non-empty-string, mixed> $data
     */
    #[TestWith([
        DtoEnumPropertiesEnum::FOO,
        DtoEnumPropertiesEnum::BAR,
        DtoEnumPropertiesEnum::BAZ,
        DtoEnumPropertiesEnum::QUX,
        ['constructorArg' => 'foo', 'constructorArgNullable' => 'bar', 'property' => 'baz', 'propertyNullable' => 'qux'],
    ])]
    #[TestWith([
        DtoEnumPropertiesEnum::FOO,
        null,
        DtoEnumPropertiesEnum::BAZ,
        null,
        ['constructorArg' => 'foo', 'constructorArgNullable' => null, 'property' => 'baz', 'propertyNullable' => null],
    ])]
    #[TestWith([
        DtoEnumPropertiesEnum::FOO,
        null,
        DtoEnumPropertiesEnum::BAZ,
        null,
        ['constructorArg' => 'foo', 'constructorArgNullable' => 'invalid', 'property' => 'baz', 'propertyNullable' => 'invalid'],
    ])]
    public function testDtoEnumProperty(
        DtoEnumPropertiesEnum $expectedConstructorArg,
        ?DtoEnumPropertiesEnum $expectedConstructorArgNullable,
        DtoEnumPropertiesEnum $expectedProperty,
        ?DtoEnumPropertiesEnum $expectedPropertyNullable,
        array $data,
    ): void {
        $object = $this->instantinator->instantiate(DtoEnumProperties::class, $data);

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
    #[TestWith([['constructorArg' => null, 'constructorArgNullable' => 'bar', 'property' => 'baz', 'propertyNullable' => 'qux']])]
    #[TestWith([['constructorArg' => 'foo', 'constructorArgNullable' => 'bar', 'property' => null, 'propertyNullable' => 'qux']])]
    #[TestWith([['constructorArg' => 123, 'constructorArgNullable' => 'bar', 'property' => null, 'propertyNullable' => 'qux']])]
    #[TestWith([['constructorArg' => 'foo', 'constructorArgNullable' => 'bar', 'property' => 123, 'propertyNullable' => 'qux']])]
    public function testDtoEnumPropertyInvalidEnumTypeError(
        array $data,
    ): void {
        self::expectException(HydratorException::class);

        try {
            $this->instantinator->instantiate(DtoEnumProperties::class, $data);
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
    #[TestWith([['constructorArg' => 'foo', 'constructorArgNullable' => 'bar', 'property' => 'invalid', 'propertyNullable' => 'qux']])]
    public function testDtoEnumPropertyValueError(
        array $data,
    ): void {
        self::expectException(HydratorException::class);

        try {
            $this->instantinator->instantiate(DtoEnumProperties::class, $data);
        } catch (\Throwable $e) {
            self::assertInstanceOf(\ValueError::class, $e->getPrevious());

            throw $e;
        }
    }
}
