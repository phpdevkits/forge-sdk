<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use DateTimeImmutable;
use InvalidArgumentException;
use JsonSerializable;
use Override;
use Throwable;

final readonly class BackupInstance implements JsonSerializable
{
    public function __construct(
        public string $id,
        public string $status,
        public bool $isPartial,
        public int $size,
        public DateTimeImmutable $finishedAt,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data  A JSON:API `BackupResource` object.
     */
    public static function from(array $data): self
    {
        $id = $data['id'] ?? null;
        if (! is_string($id)) {
            throw new InvalidArgumentException('BackupInstance data is missing the `id` field.');
        }

        $attributes = $data['attributes'] ?? null;
        if (! is_array($attributes)) {
            throw new InvalidArgumentException('BackupInstance data is missing the `attributes` object.');
        }

        $size = $attributes['size'] ?? null;
        if (! is_int($size)) {
            throw new InvalidArgumentException('BackupInstance `attributes.size` must be an integer.');
        }

        return new self(
            id: $id,
            status: self::requireString($attributes, 'status'),
            isPartial: self::requireBool($attributes, 'is_partial'),
            size: $size,
            finishedAt: self::requireDate($attributes, 'finished_at'),
        );
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     */
    private static function requireString(array $attributes, string $key): string
    {
        $value = $attributes[$key] ?? null;
        if (! is_string($value)) {
            throw new InvalidArgumentException(sprintf('BackupInstance `attributes.%s` must be a string.', $key));
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
            throw new InvalidArgumentException(sprintf('BackupInstance `attributes.%s` must be a boolean.', $key));
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
            throw new InvalidArgumentException(sprintf('BackupInstance `attributes.%s` must be a string.', $key));
        }

        try {
            return new DateTimeImmutable($value);
        } catch (Throwable $throwable) {
            throw new InvalidArgumentException(sprintf('BackupInstance `attributes.%s` is not a valid date-time.', $key), 0, $throwable);
        }
    }

    /**
     * @return array{id: string, status: string, is_partial: bool, size: int, finished_at: string}
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'is_partial' => $this->isPartial,
            'size' => $this->size,
            'finished_at' => $this->finishedAt->format(DATE_ATOM),
        ];
    }
}
