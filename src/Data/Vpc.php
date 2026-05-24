<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use InvalidArgumentException;
use JsonSerializable;
use Override;

final readonly class Vpc implements JsonSerializable
{
    /**
     * @param  list<VpcSubnet>  $subnets
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $cidrBlock,
        public array $subnets,
        public string $region,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data  A JSON:API `VpcResource` object.
     */
    public static function from(array $data): self
    {
        $id = $data['id'] ?? null;
        if (! is_string($id)) {
            throw new InvalidArgumentException('Vpc data is missing the `id` field.');
        }

        $attributes = $data['attributes'] ?? null;
        if (! is_array($attributes)) {
            throw new InvalidArgumentException('Vpc data is missing the `attributes` object.');
        }

        return new self(
            id: $id,
            name: self::requireString($attributes, 'name'),
            cidrBlock: self::requireString($attributes, 'cidrBlock'),
            subnets: self::requireSubnets($attributes, 'subnets'),
            region: self::requireString($attributes, 'region'),
        );
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     */
    private static function requireString(array $attributes, string $key): string
    {
        $value = $attributes[$key] ?? null;
        if (! is_string($value)) {
            throw new InvalidArgumentException(sprintf('Vpc `attributes.%s` must be a string.', $key));
        }

        return $value;
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     * @return list<VpcSubnet>
     */
    private static function requireSubnets(array $attributes, string $key): array
    {
        $value = $attributes[$key] ?? null;
        if (! is_array($value)) {
            throw new InvalidArgumentException(sprintf('Vpc `attributes.%s` must be an array.', $key));
        }

        $subnets = [];
        foreach ($value as $entry) {
            if (! is_array($entry)) {
                throw new InvalidArgumentException(sprintf('Vpc `attributes.%s` must contain only objects.', $key));
            }

            $subnets[] = VpcSubnet::from($entry);
        }

        return $subnets;
    }

    /**
     * @return array{id: string, name: string, cidrBlock: string, subnets: list<VpcSubnet>, region: string}
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'cidrBlock' => $this->cidrBlock,
            'subnets' => $this->subnets,
            'region' => $this->region,
        ];
    }
}
