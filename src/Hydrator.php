<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator;

use Doctrine\DBAL\Types\Types;
use Kenny1911\DoctrineDbalHydrator\Mapping\Loader;
use Kenny1911\DoctrineDbalHydrator\Name\CamelCaseToSnakeCaseNameConverter;
use Kenny1911\DoctrineDbalHydrator\Name\NameStrategy;
use Kenny1911\DoctrineDbalHydrator\Type\EnumType;

/**
 * @api
 */
final class Hydrator
{
    public function __construct(
        private readonly ObjectHydrator $objectHydrator,
        private readonly Loader $loader,
        private readonly NameStrategy $nameStrategy = new CamelCaseToSnakeCaseNameConverter(),
    ) {}

    /**
     * @template T of object
     *
     * @param class-string<T> $class
     * @param array<non-empty-string, mixed> $data
     *
     * @return T
     */
    public function hydrate(string $class, array $data): object
    {
        $mapping = $this->loader->load($class);
        /** @var array<non-empty-string, non-empty-string|EnumType> $types */
        $types = [];
        /** @var array<non-empty-string, non-empty-string> $namesMapping */
        $namesMapping = [];

        foreach ($mapping->properties as $property) {
            $types[$property->name] = $property->type ?? Types::STRING;
            $namesMapping[$property->columnName ?? $this->nameStrategy->convert($property->name)] = $property->name;
        }

        return $this->objectHydrator->hydrate(
            class: $class,
            data: $data,
            types: $types,
            mapping: $namesMapping,
        );
    }
}
