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
                if ($refClass->hasProperty($key)) {
                    $refClass->getProperty($key)->setValue($object, $value);
                }
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
     * @param array<string, mixed> $data
     *
     * @return T
     *
     * @throws \ReflectionException
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
                $constructorArgs[$parameter->name] = $data[$parameter->name];
                unset($data[$parameter->name]);
            } elseif (false === $parameter->isOptional()) {
                throw new \RuntimeException(\sprintf('Required constructor parameter "%s" of class %s is not defined.', $parameter->name, $refClass->name));
            }
        }

        return $refClass->newInstanceArgs($constructorArgs);
    }
}
