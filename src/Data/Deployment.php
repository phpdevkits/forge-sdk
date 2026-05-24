<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use DateTimeImmutable;
use InvalidArgumentException;
use JsonSerializable;
use Override;
use Throwable;

final readonly class Deployment implements JsonSerializable
{
    public function __construct(
        public string $id,
        public DeploymentCommit $commit,
        public string $type,
        public string $status,
        public ?DateTimeImmutable $startedAt,
        public ?DateTimeImmutable $endedAt,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data  A JSON:API `DeploymentResource` object.
     */
    public static function from(array $data): self
    {
        $id = $data['id'] ?? null;
        if (! is_string($id)) {
            throw new InvalidArgumentException('Deployment data is missing the `id` field.');
        }

        $attributes = $data['attributes'] ?? null;
        if (! is_array($attributes)) {
            throw new InvalidArgumentException('Deployment data is missing the `attributes` object.');
        }

        $commit = $attributes['commit'] ?? null;
        if (! is_array($commit)) {
            throw new InvalidArgumentException('Deployment `attributes.commit` must be an object.');
        }

        return new self(
            id: $id,
            commit: DeploymentCommit::from($commit),
            type: self::requireString($attributes, 'type'),
            status: self::requireString($attributes, 'status'),
            startedAt: self::optionalDate($attributes, 'started_at'),
            endedAt: self::optionalDate($attributes, 'ended_at'),
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
            throw new InvalidArgumentException(sprintf('Deployment `attributes.%s` must be a string.', $key));
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
            throw new InvalidArgumentException(sprintf('Deployment `attributes.%s` must be a string.', $key));
        }

        return self::parseDate($value, $key);
    }

    /**
     * Started/ended timestamps are null until the deployment actually starts
     * and finishes, despite the spec marking them required.
     *
     * @param  array<array-key, mixed>  $attributes
     */
    private static function optionalDate(array $attributes, string $key): ?DateTimeImmutable
    {
        $value = $attributes[$key] ?? null;
        if ($value === null) {
            return null;
        }

        if (! is_string($value)) {
            throw new InvalidArgumentException(sprintf('Deployment `attributes.%s` must be a string or null.', $key));
        }

        return self::parseDate($value, $key);
    }

    private static function parseDate(string $value, string $key): DateTimeImmutable
    {
        try {
            return new DateTimeImmutable($value);
        } catch (Throwable $throwable) {
            throw new InvalidArgumentException(sprintf('Deployment `attributes.%s` is not a valid date-time.', $key), 0, $throwable);
        }
    }

    /**
     * @return array{id: string, commit: DeploymentCommit, type: string, status: string, started_at: ?string, ended_at: ?string, created_at: string, updated_at: string}
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'commit' => $this->commit,
            'type' => $this->type,
            'status' => $this->status,
            'started_at' => $this->startedAt?->format(DATE_ATOM),
            'ended_at' => $this->endedAt?->format(DATE_ATOM),
            'created_at' => $this->createdAt->format(DATE_ATOM),
            'updated_at' => $this->updatedAt->format(DATE_ATOM),
        ];
    }
}
