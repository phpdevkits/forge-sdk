<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use DateTimeImmutable;
use InvalidArgumentException;
use JsonSerializable;
use Override;
use Throwable;

final readonly class Monitor implements JsonSerializable
{
    public function __construct(
        public string $id,
        public string $type,
        public string $operator,
        public float $threshold,
        public ?int $minutes,
        public string $notify,
        public string $status,
        public string $state,
        public ?DateTimeImmutable $stateChangedAt,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data  A JSON:API `MonitorResource` object.
     */
    public static function from(array $data): self
    {
        $id = $data['id'] ?? null;
        if (! is_string($id)) {
            throw new InvalidArgumentException('Monitor data is missing the `id` field.');
        }

        $attributes = $data['attributes'] ?? null;
        if (! is_array($attributes)) {
            throw new InvalidArgumentException('Monitor data is missing the `attributes` object.');
        }

        return new self(
            id: $id,
            type: self::requireString($attributes, 'type'),
            operator: self::requireString($attributes, 'operator'),
            threshold: self::requireFloat($attributes, 'threshold'),
            minutes: self::optionalInt($attributes, 'minutes'),
            notify: self::requireString($attributes, 'notify'),
            status: self::requireString($attributes, 'status'),
            state: self::requireString($attributes, 'state'),
            stateChangedAt: self::optionalDate($attributes, 'state_changed_at'),
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
            throw new InvalidArgumentException(sprintf('Monitor `attributes.%s` must be a string.', $key));
        }

        return $value;
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     */
    private static function requireFloat(array $attributes, string $key): float
    {
        $value = $attributes[$key] ?? null;
        if (! is_int($value) && ! is_float($value)) {
            throw new InvalidArgumentException(sprintf('Monitor `attributes.%s` must be a number.', $key));
        }

        return (float) $value;
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
            throw new InvalidArgumentException(sprintf('Monitor `attributes.%s` must be an integer or null.', $key));
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

        return self::parseDate($attributes, $key);
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     */
    private static function requireDate(array $attributes, string $key): DateTimeImmutable
    {
        $value = $attributes[$key] ?? null;
        if (! is_string($value)) {
            throw new InvalidArgumentException(sprintf('Monitor `attributes.%s` must be a string.', $key));
        }

        return self::parseDate($attributes, $key);
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     */
    private static function parseDate(array $attributes, string $key): DateTimeImmutable
    {
        $value = $attributes[$key] ?? null;
        if (! is_string($value)) {
            throw new InvalidArgumentException(sprintf('Monitor `attributes.%s` must be a string.', $key));
        }

        try {
            return new DateTimeImmutable($value);
        } catch (Throwable $throwable) {
            throw new InvalidArgumentException(sprintf('Monitor `attributes.%s` is not a valid date-time.', $key), 0, $throwable);
        }
    }

    /**
     * @return array{id: string, type: string, operator: string, threshold: float, minutes: ?int, notify: string, status: string, state: string, state_changed_at: ?string, created_at: string, updated_at: string}
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'operator' => $this->operator,
            'threshold' => $this->threshold,
            'minutes' => $this->minutes,
            'notify' => $this->notify,
            'status' => $this->status,
            'state' => $this->state,
            'state_changed_at' => $this->stateChangedAt?->format(DATE_ATOM),
            'created_at' => $this->createdAt->format(DATE_ATOM),
            'updated_at' => $this->updatedAt->format(DATE_ATOM),
        ];
    }
}
