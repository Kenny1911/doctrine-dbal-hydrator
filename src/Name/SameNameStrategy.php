<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator\Name;

/**
 * @api
 */
final class SameNameStrategy implements NameStrategy
{
    #[\Override]
    public function convert(string $name): string
    {
        return $name;
    }
}
