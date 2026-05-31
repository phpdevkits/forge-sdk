<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use PhpDevKits\ForgeSdk\Enums\BackupFrequency;

/**
 * Input payload for PUT /orgs/{org}/servers/{server}/database/backups/{id}.
 *
 * Forge's spec marks `storage_provider_id`, `frequency`, `retention`, and
 * `database_ids` as required on update — this is effectively a full
 * replacement, not a partial patch. See {@see CreateBackupConfigurationData}
 * for field-by-field semantics.
 */
final readonly class UpdateBackupConfigurationData
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
