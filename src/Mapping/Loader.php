<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator\Mapping;

/**
 * @api
 */
interface Loader
{
    /**
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return Mapping<T>
     */
    public function load(string $class): Mapping;
}
