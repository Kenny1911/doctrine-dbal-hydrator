<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator\Tests;

use Doctrine\DBAL\Platforms\SQLitePlatform;
use Kenny1911\DoctrineDbalHydrator\Hydrator;
use Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\DtoConstructor;
use Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\DtoConstructorAndProperties;
use Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\DtoConstructorPromotedProperties;
use Kenny1911\DoctrineDbalHydrator\Tests\DtoClass\DtoOnlyProperties;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @psalm-internal Kenny1911\DoctrineDbalHydrator\Tests
 */
final class HydratorTest extends TestCase
{
    private Hydrator $hydrator;

    #[\Override]
    protected function setUp(): void
    {
        $this->hydrator = new Hydrator(new SQLitePlatform());
    }

    /**
     * @param array<non-empty-string, mixed> $data
     * @param array<non-empty-string, non-empty-string> $types
     */
    #[TestWith(['Foo', 0, ['foo' => 'Foo'], []])]
    #[TestWith(['Foo', 123, ['foo' => 'Foo', 'bar' => 123], []])]
    #[TestWith(['Foo', 123, ['foo' => 'Foo', 'bar' => '123'], ['bar' => 'integer']])]
    #[TestWith(['Foo', 123, ['foo' => 'Foo', 'bar' => '123'], ['foo' => 'text', 'bar' => 'integer']])]
    public function testHydrateDtoConstructor(string $expectedFoo, int $expectedBar, array $data, array $types): void
    {
        $object = $this->hydrator->hydrate(DtoConstructor::class, $data, $types);

        self::assertInstanceOf(DtoConstructor::class, $object);
        self::assertSame($expectedFoo, $object->foo);
        self::assertSame($expectedBar, $object->bar);
    }

    /**
     * @param array<non-empty-string, mixed> $data
     * @param array<non-empty-string, non-empty-string> $types
     */
    #[TestWith([
        'Foo',
        123,
        new \DateTimeImmutable('2025-01-01 00:00:00'),
        ['foo' => 'Foo', 'bar' => 123, 'baz' => new \DateTimeImmutable('2025-01-01 00:00:00')],
        ['baz' => 'datetime_immutable'],
    ])]
    #[TestWith(['Foo', 0, null, ['foo' => 'Foo'], []])]
    public function testHydrateDtoConstructorAndProperties(
        string $expectedFoo,
        int $expectedBar,
        ?\DateTimeImmutable $expectedBaz,
        array $data,
        array $types,
    ): void {
        $object = $this->hydrator->hydrate(DtoConstructorAndProperties::class, $data, $types);

        self::assertInstanceOf(DtoConstructorAndProperties::class, $object);
        self::assertSame($expectedFoo, $object->foo);
        self::assertSame($expectedBar, $object->bar);
        self::assertEquals($expectedBaz, $object->baz);
    }

    /**
     * @param array<non-empty-string, mixed> $data
     * @param array<non-empty-string, non-empty-string> $types
     */
    #[TestWith(['Foo', 0, ['foo' => 'Foo'], []])]
    #[TestWith(['Foo', 123, ['foo' => 'Foo', 'bar' => 123], []])]
    #[TestWith(['Foo', 123, ['foo' => 'Foo', 'bar' => '123'], ['bar' => 'integer']])]
    #[TestWith(['Foo', 123, ['foo' => 'Foo', 'bar' => '123'], ['foo' => 'text', 'bar' => 'integer']])]
    public function testHydrateDtoConstructorPromotedProperties(
        string $expectedFoo,
        int $expectedBar,
        array $data,
        array $types,
    ): void {
        $object = $this->hydrator->hydrate(DtoConstructorPromotedProperties::class, $data, $types);

        self::assertInstanceOf(DtoConstructorPromotedProperties::class, $object);
        self::assertSame($expectedFoo, $object->foo);
        self::assertSame($expectedBar, $object->bar);
    }

    /**
     * @param array<non-empty-string, mixed> $data
     * @param array<non-empty-string, non-empty-string> $types
     */
    #[TestWith([null, null, [], []])]
    #[TestWith(['Foo', null, ['foo' => 'Foo'], []])]
    #[TestWith(['Foo', 123, ['foo' => 'Foo', 'bar' => 123], []])]
    #[TestWith(['Foo', 123, ['foo' => 'Foo', 'bar' => '123'], ['bar' => 'integer']])]
    #[TestWith(['Foo', 123, ['foo' => 'Foo', 'bar' => '123'], ['foo' => 'text', 'bar' => 'integer']])]
    public function testHydrateDtoOnlyProperties(
        ?string $expectedFoo,
        ?int $expectedBar,
        array $data,
        array $types,
    ): void {
        $object = $this->hydrator->hydrate(DtoOnlyProperties::class, $data, $types);

        self::assertInstanceOf(DtoOnlyProperties::class, $object);
        self::assertSame($expectedFoo, $object->foo);
        self::assertSame($expectedBar, $object->bar);
    }
}
