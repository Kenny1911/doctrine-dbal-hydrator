<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator\Mapping;

use Kenny1911\DoctrineDbalHydrator\Mapping\Attribute\Column;
use Kenny1911\DoctrineDbalHydrator\Type\EnumType;

/**
 * @api
 */
final class AttributeLoader implements Loader
{
    /**
     * @var array<class-string, Mapping>
     */
    private array $mappings = [];

    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return Mapping<T>
     * @throws \ReflectionException
     */
    #[\Override]
    public function load(string $class): Mapping
    {
        if (isset($this->mappings[$class])) {
            /** @var Mapping<T> */
            return $this->mappings[$class];
        }

        $refClass = new \ReflectionClass($class);
        /** @var list<Property> $properties */
        $properties = [];

        foreach ($refClass->getProperties() as $refProp) {
            $refAttribute = $refProp->getAttributes(Column::class)[0] ?? null;

            if (null === $refAttribute) {
                continue;
            }

            $column = $refAttribute->newInstance();

            if (false === $column instanceof Column) {
                continue;
            }

            $refType = $refProp->getType();

            if ($refType instanceof \ReflectionNamedType && is_a($refType->getName(), \BackedEnum::class, true)) {
                $type = new EnumType(
                    enum: $refType->getName(),
                    type: $column->type,
                );
            } else {
                $type = $column->type;
            }

            $properties[] = new Property(
                name: $refProp->getName(),
                type: $type,
                columnName: $column->name,
            );
        }

        return $this->mappings[$class] = new Mapping(
            class: $class,
            properties: $properties,
        );
    }
}
