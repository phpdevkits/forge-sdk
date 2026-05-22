<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use DateTimeImmutable;
use InvalidArgumentException;
use JsonSerializable;
use Override;
use Throwable;

final readonly class SshKey implements JsonSerializable
{
    public function __construct(
        public string $id,
        public string $name,
        public string $user,
        public string $status,
        public ?int $createdBy,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data  A JSON:API `KeyResource` object.
     */
    public static function from(array $data): self
    {
        $id = $data['id'] ?? null;
        if (! is_string($id)) {
            throw new InvalidArgumentException('SshKey data is missing the `id` field.');
        }

        $attributes = $data['attributes'] ?? null;
        if (! is_array($attributes)) {
            throw new InvalidArgumentException('SshKey data is missing the `attributes` object.');
        }

        return new self(
            id: $id,
            name: self::requireString($attributes, 'name'),
            user: self::requireString($attributes, 'user'),
            status: self::requireString($attributes, 'status'),
            createdBy: self::optionalInt($attributes, 'created_by'),
            createdAt: self::requireDate($attributes, 'created_at'),
            updatedAt: self::requireDate($attributes, 'updated_at'),
        );
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     */
    private static function requireString(array $attributes, string $key): string
    {
        $value = $attributes[$key] ?? null;
        if (! is_string($value)) {
            throw new InvalidArgumentException(sprintf('SshKey `attributes.%s` must be a string.', $key));
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
            throw new InvalidArgumentException(sprintf('SshKey `attributes.%s` must be an integer or null.', $key));
        }

        return $value;
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     */
    private static function requireDate(array $attributes, string $key): DateTimeImmutable
    {
        $value = $attributes[$key] ?? null;
        if (! is_string($value)) {
            throw new InvalidArgumentException(sprintf('SshKey `attributes.%s` must be a string.', $key));
        }

        try {
            return new DateTimeImmutable($value);
        } catch (Throwable $throwable) {
            throw new InvalidArgumentException(sprintf('SshKey `attributes.%s` is not a valid date-time.', $key), 0, $throwable);
        }
    }

    /**
     * @return array{id: string, name: string, user: string, status: string, created_by: ?int, created_at: string, updated_at: string}
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'user' => $this->user,
            'status' => $this->status,
            'created_by' => $this->createdBy,
            'created_at' => $this->createdAt->format(DATE_ATOM),
            'updated_at' => $this->updatedAt->format(DATE_ATOM),
        ];
    }
}
