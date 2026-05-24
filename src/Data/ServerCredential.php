<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use DateTimeImmutable;
use InvalidArgumentException;
use JsonSerializable;
use Override;
use Throwable;

final readonly class ServerCredential implements JsonSerializable
{
    public function __construct(
        public string $id,
        public string $name,
        public string $provider,
        public bool $inUse,
        public ?DateTimeImmutable $createdAt,
        public ?DateTimeImmutable $updatedAt,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data  A JSON:API `ServerCredentialResource` object.
     */
    public static function from(array $data): self
    {
        $id = $data['id'] ?? null;
        if (! is_string($id)) {
            throw new InvalidArgumentException('ServerCredential data is missing the `id` field.');
        }

        $attributes = $data['attributes'] ?? null;
        if (! is_array($attributes)) {
            throw new InvalidArgumentException('ServerCredential data is missing the `attributes` object.');
        }

        return new self(
            id: $id,
            name: self::requireString($attributes, 'name'),
            provider: self::requireString($attributes, 'provider'),
            inUse: self::requireBool($attributes, 'in_use'),
            createdAt: self::optionalDate($attributes, 'created_at'),
            updatedAt: self::optionalDate($attributes, 'updated_at'),
        );
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     */
    private static function requireString(array $attributes, string $key): string
    {
        $value = $attributes[$key] ?? null;
        if (! is_string($value)) {
            throw new InvalidArgumentException(sprintf('ServerCredential `attributes.%s` must be a string.', $key));
        }

        return $value;
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     */
    private static function requireBool(array $attributes, string $key): bool
    {
        $value = $attributes[$key] ?? null;
        if (! is_bool($value)) {
            throw new InvalidArgumentException(sprintf('ServerCredential `attributes.%s` must be a boolean.', $key));
        }

        return $value;
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     */
    private static function optionalDate(array $attributes, string $key): ?DateTimeImmutable
    {
        $value = $attributes[$key] ?? null;
        if ($value === null) {
            return null;
        }

        if (! is_string($value)) {
            throw new InvalidArgumentException(sprintf('ServerCredential `attributes.%s` must be a string or null.', $key));
        }

        try {
            return new DateTimeImmutable($value);
        } catch (Throwable $throwable) {
            throw new InvalidArgumentException(sprintf('ServerCredential `attributes.%s` is not a valid date-time.', $key), 0, $throwable);
        }
    }

    /**
     * @return array{id: string, name: string, provider: string, in_use: bool, created_at: ?string, updated_at: ?string}
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'provider' => $this->provider,
            'in_use' => $this->inUse,
            'created_at' => $this->createdAt?->format(DATE_ATOM),
            'updated_at' => $this->updatedAt?->format(DATE_ATOM),
        ];
    }
}
