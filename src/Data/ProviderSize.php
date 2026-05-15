<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use InvalidArgumentException;
use JsonSerializable;
use Override;

/**
 * A provider size.
 *
 * Forge returns the FULL attribute set for `/providers/{id}/sizes` and
 * `/providers/{id}/sizes/{size}`, but only `name` for the region-scoped
 * variants (`/providers/{id}/regions/{r}/sizes[/{size}]`) — those endpoints
 * are a thin "what sizes are available in this region" filter and refer the
 * caller to the full size resource via `links.self.href`. Every attribute
 * other than `id` / `name` is therefore nullable on the DTO.
 */
final readonly class ProviderSize implements JsonSerializable
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $code,
        public ?string $series,
        public ?string $category,
        public ?int $cpus,
        public ?string $diskType,
        public ?string $architecture,
        public ?int $ram,
        public ?int $disk,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data  A JSON:API `ProviderSizeResource` object.
     */
    public static function from(array $data): self
    {
        $id = $data['id'] ?? null;
        if (! is_string($id)) {
            throw new InvalidArgumentException('ProviderSize data is missing the `id` field.');
        }

        $attributes = $data['attributes'] ?? null;
        if (! is_array($attributes)) {
            throw new InvalidArgumentException('ProviderSize data is missing the `attributes` object.');
        }

        $name = $attributes['name'] ?? null;
        if (! is_string($name)) {
            throw new InvalidArgumentException('ProviderSize `attributes.name` must be a string.');
        }

        return new self(
            id: $id,
            name: $name,
            code: self::optionalString($attributes, 'code'),
            series: self::optionalString($attributes, 'series'),
            category: self::optionalString($attributes, 'category'),
            cpus: self::optionalInt($attributes, 'cpus'),
            diskType: self::optionalString($attributes, 'disk_type'),
            architecture: self::optionalString($attributes, 'architecture'),
            ram: self::optionalInt($attributes, 'ram'),
            disk: self::optionalInt($attributes, 'disk'),
        );
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
            throw new InvalidArgumentException(sprintf('ProviderSize `attributes.%s` must be a string or null.', $key));
        }

        return $value;
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     */
    private static function optionalInt(array $attributes, string $key): ?int
    {
        $value = $attributes[$key] ?? null;
        if ($value === null) {
            return null;
        }

        if (! is_int($value)) {
            throw new InvalidArgumentException(sprintf('ProviderSize `attributes.%s` must be an integer or null.', $key));
        }

        return $value;
    }

    /**
     * @return array{id: string, name: string, code: ?string, series: ?string, category: ?string, cpus: ?int, disk_type: ?string, architecture: ?string, ram: ?int, disk: ?int}
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'series' => $this->series,
            'category' => $this->category,
            'cpus' => $this->cpus,
            'disk_type' => $this->diskType,
            'architecture' => $this->architecture,
            'ram' => $this->ram,
            'disk' => $this->disk,
        ];
    }
}
