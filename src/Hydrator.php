<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * @api
 */
final class Hydrator
{
    public function __construct(
        private readonly AbstractPlatform $platform,
        private readonly Instantinator $instantinator = new SimpleInstantinator(),
    ) {}

    /**
     * @throws Exception
     */
    public function createByConnection(
        Connection $connection,
        Instantinator $instantinator = new SimpleInstantinator(),
    ): self {
        return new self(
            platform: $connection->getDatabasePlatform(),
            instantinator: $instantinator,
        );
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $class
     * @param array<non-empty-string, mixed> $data
     * @param array<non-empty-string, non-empty-string> $types
     *
     * @return T
     *
     * @throws HydratorException
     */
    public function hydrate(string $class, array $data, array $types = []): object
    {
        try {
            $platform = $this->platform;

            $convertedData = [];

            foreach ($data as $key => $value) {
                if (isset($types[$key])) {
                    $convertedData[$key] = Type::getType($types[$key])->convertToPHPValue($value, $platform);
                } else {
                    $convertedData[$key] = $value;
                }
            }

            return $this->instantinator->instantiate($class, $convertedData);
        } catch (\Exception|Exception $e) {
            throw new HydratorException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
