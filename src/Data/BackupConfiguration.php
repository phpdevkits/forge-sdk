<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use InvalidArgumentException;
use JsonSerializable;
use Override;

final readonly class BackupConfiguration implements JsonSerializable
{
    /**
     * @param  list<int>  $databaseIds
     */
    public function __construct(
        public string $id,
        public string $name,
        public ?int $storageProviderId,
        public string $provider,
        public ?string $bucket,
        public string $directory,
        public string $schedule,
        public string $displayableSchedule,
        public string $nextRunTime,
        public string $status,
        public ?int $dayOfWeek,
        public ?string $time,
        public ?string $cronSchedule,
        public array $databaseIds,
        public int $retention,
        public ?string $notifyEmail,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data  A JSON:API `BackupConfigurationResource` object.
     */
    public static function from(array $data): self
    {
        $id = $data['id'] ?? null;
        if (! is_string($id)) {
            throw new InvalidArgumentException('BackupConfiguration data is missing the `id` field.');
        }

        $attributes = $data['attributes'] ?? null;
        if (! is_array($attributes)) {
            throw new InvalidArgumentException('BackupConfiguration data is missing the `attributes` object.');
        }

        return new self(
            id: $id,
            name: self::requireString($attributes, 'name'),
            storageProviderId: self::optionalInt($attributes, 'storage_provider_id'),
            provider: self::requireString($attributes, 'provider'),
            bucket: self::optionalString($attributes, 'bucket'),
            directory: self::requireString($attributes, 'directory'),
            schedule: self::requireString($attributes, 'schedule'),
            displayableSchedule: self::requireString($attributes, 'displayable_schedule'),
            nextRunTime: self::requireString($attributes, 'next_run_time'),
            status: self::requireString($attributes, 'status'),
            dayOfWeek: self::optionalInt($attributes, 'day_of_week'),
            time: self::optionalString($attributes, 'time'),
            cronSchedule: self::optionalString($attributes, 'cron_schedule'),
            databaseIds: self::requireIntList($attributes, 'database_ids'),
            retention: self::requireInt($attributes, 'retention'),
            notifyEmail: self::optionalString($attributes, 'notify_email'),
        );
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     */
    private static function requireString(array $attributes, string $key): string
    {
        $value = $attributes[$key] ?? null;
        if (! is_string($value)) {
            throw new InvalidArgumentException(sprintf('BackupConfiguration `attributes.%s` must be a string.', $key));
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
            throw new InvalidArgumentException(sprintf('BackupConfiguration `attributes.%s` must be a string or null.', $key));
        }

        return $value;
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     */
    private static function requireInt(array $attributes, string $key): int
    {
        $value = $attributes[$key] ?? null;
        if (! is_int($value)) {
            throw new InvalidArgumentException(sprintf('BackupConfiguration `attributes.%s` must be an integer.', $key));
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
            throw new InvalidArgumentException(sprintf('BackupConfiguration `attributes.%s` must be an integer or null.', $key));
        }

        return $value;
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     * @return list<int>
     */
    private static function requireIntList(array $attributes, string $key): array
    {
        $value = $attributes[$key] ?? null;
        if (! is_array($value)) {
            throw new InvalidArgumentException(sprintf('BackupConfiguration `attributes.%s` must be an array.', $key));
        }

        $ids = [];
        foreach ($value as $item) {
            if (! is_int($item)) {
                throw new InvalidArgumentException(sprintf('BackupConfiguration `attributes.%s` must contain only integers.', $key));
            }

            $ids[] = $item;
        }

        return $ids;
    }

    /**
     * @return array{id: string, name: string, storage_provider_id: ?int, provider: string, bucket: ?string, directory: string, schedule: string, displayable_schedule: string, next_run_time: string, status: string, day_of_week: ?int, time: ?string, cron_schedule: ?string, database_ids: list<int>, retention: int, notify_email: ?string}
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'storage_provider_id' => $this->storageProviderId,
            'provider' => $this->provider,
            'bucket' => $this->bucket,
            'directory' => $this->directory,
            'schedule' => $this->schedule,
            'displayable_schedule' => $this->displayableSchedule,
            'next_run_time' => $this->nextRunTime,
            'status' => $this->status,
            'day_of_week' => $this->dayOfWeek,
            'time' => $this->time,
            'cron_schedule' => $this->cronSchedule,
            'database_ids' => $this->databaseIds,
            'retention' => $this->retention,
            'notify_email' => $this->notifyEmail,
        ];
    }
}
