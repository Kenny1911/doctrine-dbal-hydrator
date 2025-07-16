<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator;

/**
 * @internal
 * @psalm-internal Kenny1911\DoctrineDbalHydrator
 */
final class SimpleInstantinator implements Instantinator
{
    #[\Override]
    public function instantiate(string $class, array $data): object
    {
        try {
            $refClass = new \ReflectionClass($class);

            $object = $this->newInstance($refClass, $data);

            foreach ($data as $key => $value) {
                $refProp = $refClass->getProperty($key);
                $refProp->setValue($object, $this->prepareValue($refProp->getType(), $value));
            }

            return $object;
        } catch (\Throwable $e) {
            throw new HydratorException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * @template T of object
     *
     * @param \ReflectionClass<T> $refClass
     * @param array<non-empty-string, mixed> $data
     *
     * @return T
     *
     * @throws \ReflectionException
     * @throws \Throwable
     */
    private function newInstance(\ReflectionClass $refClass, array &$data): object
    {
        $constructor = $refClass->getConstructor();

        if (null === $constructor) {
            return $refClass->newInstance();
        }

        if (false === $constructor->isPublic()) {
            throw new \RuntimeException('Constructor must be public.');
        }

        $constructorArgs = [];

        foreach ($constructor->getParameters() as $parameter) {
            if (\array_key_exists($parameter->name, $data)) {
                $constructorArgs[$parameter->name] = $this->prepareValue($parameter->getType(), $data[$parameter->name]);
                unset($data[$parameter->name]);
            } elseif (false === $parameter->isOptional()) {
                throw new \RuntimeException(\sprintf('Required constructor parameter "%s" of class %s is not defined.', $parameter->name, $refClass->name));
            }
        }

        return $refClass->newInstanceArgs($constructorArgs);
    }

    /**
     * @throws \Throwable
     */
    private function prepareValue(?\ReflectionType $type, mixed $value): mixed
    {
        if (
            $type instanceof \ReflectionNamedType
            && is_a($type->getName(), \BackedEnum::class, true)
        ) {
            /** @var class-string<\BackedEnum> $enum */
            $enum = $type->getName();

            try {
                /** @psalm-suppress MixedArgument */
                return $enum::from($value);
            } catch (\Throwable $e) {
                if ($type->allowsNull()) {
                    return null;
                }

                throw $e;
            }
        }

        return $value;
    }
}
