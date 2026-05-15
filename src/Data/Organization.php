<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use DateTimeImmutable;
use InvalidArgumentException;
use JsonSerializable;
use Override;
use Throwable;

final readonly class Organization implements JsonSerializable
{
    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public ?DateTimeImmutable $createdAt,
        public ?DateTimeImmutable $updatedAt,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data  A JSON:API `OrganizationResource` object
     *                                         (the shape under each `data[*]` entry).
     */
    public static function from(array $data): self
    {
        $id = $data['id'] ?? null;
        if (! is_string($id)) {
            throw new InvalidArgumentException('Organization data is missing the `id` field.');
        }

        $attributes = $data['attributes'] ?? null;
        if (! is_array($attributes)) {
            throw new InvalidArgumentException('Organization data is missing the `attributes` object.');
        }

        $name = $attributes['name'] ?? null;
        if (! is_string($name)) {
            throw new InvalidArgumentException('Organization `attributes.name` must be a string.');
        }

        $slug = $attributes['slug'] ?? null;
        if (! is_string($slug)) {
            throw new InvalidArgumentException('Organization `attributes.slug` must be a string.');
        }

        return new self(
            id: $id,
            name: $name,
            slug: $slug,
            createdAt: self::parseDate($attributes['created_at'] ?? null, 'created_at'),
            updatedAt: self::parseDate($attributes['updated_at'] ?? null, 'updated_at'),
        );
    }

    private static function parseDate(mixed $value, string $field): ?DateTimeImmutable
    {
        if ($value === null) {
            return null;
        }

        if (! is_string($value)) {
            throw new InvalidArgumentException(sprintf('Organization `attributes.%s` must be a string or null.', $field));
        }

        try {
            return new DateTimeImmutable($value);
        } catch (Throwable $throwable) {
            throw new InvalidArgumentException(sprintf('Organization `attributes.%s` is not a valid date-time.', $field), 0, $throwable);
        }
    }

    /**
     * @return array{id: string, name: string, slug: string, created_at: ?string, updated_at: ?string}
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'created_at' => $this->createdAt?->format(DATE_ATOM),
            'updated_at' => $this->updatedAt?->format(DATE_ATOM),
        ];
    }
}
