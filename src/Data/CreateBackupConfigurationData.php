<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use PhpDevKits\ForgeSdk\Enums\BackupFrequency;

/**
 * Input payload for POST /orgs/{org}/servers/{server}/database/backups.
 *
 * `storage_provider_id` references a `StorageProvider` already configured
 * on the organization (S3, Spaces, Hetzner, etc.) — no inline credentials.
 * `database_ids` must contain at least one schema id to back up.
 *
 * `day` is the weekly day-of-week (`"0"`-`"6"`) when `frequency` is
 * weekly; `time` is the 30-minute slot (`"00:00"`-`"23:30"`) when
 * `frequency` is daily or weekly; `cron` is the raw expression when
 * `frequency` is custom.
 */
final readonly class CreateBackupConfigurationData
{
    /**
     * @param  list<int>  $databaseIds
     */
    public function __construct(
        public int $storageProviderId,
        public BackupFrequency $frequency,
        public int $retention,
        public array $databaseIds,
        public ?string $name = null,
        public ?string $bucket = null,
        public ?string $directory = null,
        public ?string $day = null,
        public ?string $time = null,
        public ?string $cron = null,
        public ?string $notificationEmail = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [
            'storage_provider_id' => $this->storageProviderId,
            'frequency' => $this->frequency->value,
            'retention' => $this->retention,
            'database_ids' => $this->databaseIds,
        ];

        if ($this->name !== null) {
            $payload['name'] = $this->name;
        }

        if ($this->bucket !== null) {
            $payload['bucket'] = $this->bucket;
        }

        if ($this->directory !== null) {
            $payload['directory'] = $this->directory;
        }

        if ($this->day !== null) {
            $payload['day'] = $this->day;
        }

        if ($this->time !== null) {
            $payload['time'] = $this->time;
        }

        if ($this->cron !== null) {
            $payload['cron'] = $this->cron;
        }

        if ($this->notificationEmail !== null) {
            $payload['notification_email'] = $this->notificationEmail;
        }

        return $payload;
    }
}
