<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use InvalidArgumentException;
use JsonSerializable;
use Override;

final readonly class Provider implements JsonSerializable
{
    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public ?string $simpleName,
        public string $currency,
        public string $currencySymbol,
        public ?string $defaultSizeCode,
        public ?string $defaultRegionCode,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data  A JSON:API `ProviderResource` object.
     */
    public static function from(array $data): self
    {
        $id = $data['id'] ?? null;
        if (! is_string($id)) {
            throw new InvalidArgumentException('Provider data is missing the `id` field.');
        }

        $attributes = $data['attributes'] ?? null;
        if (! is_array($attributes)) {
            throw new InvalidArgumentException('Provider data is missing the `attributes` object.');
        }

        return new self(
            id: $id,
            name: self::requireString($attributes, 'name'),
            slug: self::requireString($attributes, 'slug'),
            simpleName: self::optionalString($attributes, 'simple_name'),
            currency: self::requireString($attributes, 'currency'),
            currencySymbol: self::requireString($attributes, 'currency_symbol'),
            defaultSizeCode: self::optionalString($attributes, 'default_size_code'),
            defaultRegionCode: self::optionalString($attributes, 'default_region_code'),
        );
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     */
    private static function requireString(array $attributes, string $key): string
    {
        $value = $attributes[$key] ?? null;
        if (! is_string($value)) {
            throw new InvalidArgumentException(sprintf('Provider `attributes.%s` must be a string.', $key));
        }

        return $value;
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     */
    private static function optionalString(array $attributes, string $key): ?string
    {
        $value = $attributes[$key] ?? null;
        if ($value === null) {
            return null;
        }

        if (! is_string($value)) {
            throw new InvalidArgumentException(sprintf('Provider `attributes.%s` must be a string or null.', $key));
        }

        return $value;
    }

    /**
     * @return array{id: string, name: string, slug: string, simple_name: ?string, currency: string, currency_symbol: string, default_size_code: ?string, default_region_code: ?string}
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'simple_name' => $this->simpleName,
            'currency' => $this->currency,
            'currency_symbol' => $this->currencySymbol,
            'default_size_code' => $this->defaultSizeCode,
            'default_region_code' => $this->defaultRegionCode,
        ];
    }
}
