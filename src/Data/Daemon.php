<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use DateTimeImmutable;
use InvalidArgumentException;
use JsonSerializable;
use Override;
use Throwable;

final readonly class Daemon implements JsonSerializable
{
    public function __construct(
        public string $id,
        public string $command,
        public string $user,
        public ?string $directory,
        public int $processes,
        public string $status,
        public DateTimeImmutable $createdAt,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data  A JSON:API `BackgroundProcessResource` object.
     */
    public static function from(array $data): self
    {
        $id = $data['id'] ?? null;
        if (! is_string($id)) {
            throw new InvalidArgumentException('Daemon data is missing the `id` field.');
        }

        $attributes = $data['attributes'] ?? null;
        if (! is_array($attributes)) {
            throw new InvalidArgumentException('Daemon data is missing the `attributes` object.');
        }

        $processes = $attributes['processes'] ?? null;
        if (! is_int($processes)) {
            throw new InvalidArgumentException('Daemon `attributes.processes` must be an integer.');
        }

        return new self(
            id: $id,
            command: self::requireString($attributes, 'command'),
            user: self::requireString($attributes, 'user'),
            directory: self::optionalString($attributes, 'directory'),
            processes: $processes,
            status: self::requireString($attributes, 'status'),
            createdAt: self::requireDate($attributes, 'created_at'),
        );
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     */
    private static function requireString(array $attributes, string $key): string
    {
        $value = $attributes[$key] ?? null;
        if (! is_string($value)) {
            throw new InvalidArgumentException(sprintf('Daemon `attributes.%s` must be a string.', $key));
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
            throw new InvalidArgumentException(sprintf('Daemon `attributes.%s` must be a string or null.', $key));
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
            throw new InvalidArgumentException(sprintf('Daemon `attributes.%s` must be a string.', $key));
        }

        try {
            return new DateTimeImmutable($value);
        } catch (Throwable $throwable) {
            throw new InvalidArgumentException(sprintf('Daemon `attributes.%s` is not a valid date-time.', $key), 0, $throwable);
        }
    }

    /**
     * @return array{id: string, command: string, user: string, directory: ?string, processes: int, status: string, created_at: string}
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'command' => $this->command,
            'user' => $this->user,
            'directory' => $this->directory,
            'processes' => $this->processes,
            'status' => $this->status,
            'created_at' => $this->createdAt->format(DATE_ATOM),
        ];
    }
}
