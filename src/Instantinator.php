<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator;

/**
 * @api
 */
interface Instantinator
{
    /**
     * @template T of object
     *
     * @param class-string<T> $class
     * @param array<string, mixed> $data
     *
     * @return T
     *
     * @throws HydratorException
     */
    public function instantiate(string $class, array $data): object;
}
