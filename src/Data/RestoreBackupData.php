<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

/**
 * Input payload for POST /orgs/{org}/servers/{server}/database/backups/{backupConfiguration}/instances/{backup}/restores.
 *
 * Identifies the target database the backup should be restored into.
 */
final readonly class RestoreBackupData
{
    public function __construct(
        public int $databaseId,
    ) {}

    /**
     * @return array{database_id: int}
     */
    public function toArray(): array
    {
        return [
            'database_id' => $this->databaseId,
        ];
    }
}
