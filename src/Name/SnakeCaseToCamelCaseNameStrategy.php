<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator\Name;

/**
 * @api
 */
final class SnakeCaseToCamelCaseNameStrategy implements NameStrategy
{
    #[\Override]
    public function convert(string $name): string
    {
        $name = ucwords($name, '_');
        $name = lcfirst($name);
        $name = str_replace('_', '', $name);

        if ('' === $name) {
            throw new \LogicException('Converted string is empty.');
        }

        return $name;
    }
}
