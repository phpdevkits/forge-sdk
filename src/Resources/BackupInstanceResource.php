<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use PhpDevKits\ForgeSdk\Data\BackupInstance;
use PhpDevKits\ForgeSdk\Data\RestoreBackupData;
use PhpDevKits\ForgeSdk\Requests\Servers\DeleteBackupInstance;
use PhpDevKits\ForgeSdk\Requests\Servers\GetBackupInstance;
use PhpDevKits\ForgeSdk\Requests\Servers\RestoreBackup;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class BackupInstanceResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        private readonly string $organization,
        private readonly int|string $serverId,
        private readonly int|string $backupConfigurationId,
        private readonly int|string $backupId,
    ) {
        parent::__construct($connector);
    }

    /**
     * @throws Throwable
     */
    public function get(): BackupInstance
    {
        $response = $this->connector->send(new GetBackupInstance($this->organization, $this->serverId, $this->backupConfigurationId, $this->backupId));

        /** @var array<array-key, mixed> $data */
        $data = $response->json('data');

        return BackupInstance::from($data);
    }

    /**
     * Delete this backup instance. Forge processes the deletion asynchronously
     * (202), so this returns void.
     *
     * @throws Throwable
     */
    public function delete(): void
    {
        $this->connector->send(new DeleteBackupInstance($this->organization, $this->serverId, $this->backupConfigurationId, $this->backupId));
    }

    /**
     * Restore this backup into the database identified by
     * {@see RestoreBackupData::$databaseId}. Forge processes the restore
     * asynchronously (202) and overwrites the target database — call with
     * care.
     *
     * @throws Throwable
     */
    public function restore(RestoreBackupData $data): void
    {
        $this->connector->send(new RestoreBackup($this->organization, $this->serverId, $this->backupConfigurationId, $this->backupId, $data));
    }
}
