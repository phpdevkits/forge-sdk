<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use InvalidArgumentException;
use JsonSerializable;
use Override;

/**
 * An entry in the `subnets` array on a {@see Vpc}.
 */
final readonly class VpcSubnet implements JsonSerializable
{
    public function __construct(
        public string $id,
        public string $name,
        public string $cidrBlock,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function from(array $data): self
    {
        return new self(
            id: self::requireString($data, 'id'),
            name: self::requireString($data, 'name'),
            cidrBlock: self::requireString($data, 'cidrBlock'),
        );
    }

    /**
     * @param  array<array-key, mixed>  $data
     */
    private static function requireString(array $data, string $key): string
    {
        $value = $data[$key] ?? null;
        if (! is_string($value)) {
            throw new InvalidArgumentException(sprintf('VpcSubnet `%s` must be a string.', $key));
        }

        return $value;
    }

    /**
     * @return array{id: string, name: string, cidrBlock: string}
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'cidrBlock' => $this->cidrBlock,
        ];
    }
}
