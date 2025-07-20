<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator\Name;

/**
 * @api
 */
interface NameStrategy
{
    /**
     * @param non-empty-string $name
     *
     * @return non-empty-string
     */
    public function convert(string $name): string;
}
