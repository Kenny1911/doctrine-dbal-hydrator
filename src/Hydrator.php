<?php

declare(strict_types=1);

namespace Kenny1911\DoctrineDbalHydrator;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Kenny1911\DoctrineDbalHydrator\Type\EnumType;

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
    public static function createByConnection(
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
     * @param array<non-empty-string, non-empty-string|EnumType> $types
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
                $type = $types[$key] ?? null;

                if (null === $value) {
                    $convertedData[$key] = null;
                } elseif ($type instanceof EnumType) {
                    /** @psalm-suppress MixedArgument */
                    $convertedData[$key] = $type->enum::from(Type::getType($type->type)->convertToPHPValue($value, $platform));
                } elseif (\is_string($type)) {
                    $convertedData[$key] = Type::getType($type)->convertToPHPValue($value, $platform);
                } else {
                    $convertedData[$key] = $value;
                }
            }

            return $this->instantinator->instantiate($class, $convertedData);
        } catch (HydratorException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new HydratorException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }
}
