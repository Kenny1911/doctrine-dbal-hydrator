<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator\Name;

/**
 * @api
 */
final class CamelCaseToSnakeCaseNameConverter implements NameStrategy
{
    #[\Override]
    public function convert(string $name): string
    {
        $name = (string) preg_replace('/(?<!^)[A-Z]/', '_$0', $name);
        $name = (string) preg_replace('/_{2,}/', '_', $name);
        $name = mb_strtolower($name);
        $name = mb_trim($name, '_');

        if ('' === $name) {
            throw new \LogicException('Converted string is empty.');
        }

        return $name;
    }
}
