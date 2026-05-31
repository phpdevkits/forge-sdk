<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use PhpDevKits\ForgeSdk\Data\BackupConfiguration;
use PhpDevKits\ForgeSdk\Data\UpdateBackupConfigurationData;
use PhpDevKits\ForgeSdk\Requests\Servers\DeleteBackupConfiguration;
use PhpDevKits\ForgeSdk\Requests\Servers\GetBackupConfiguration;
use PhpDevKits\ForgeSdk\Requests\Servers\UpdateBackupConfiguration;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class BackupConfigurationResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        private readonly string $organization,
        private readonly int|string $serverId,
        private readonly int|string $backupConfigurationId,
    ) {
        parent::__construct($connector);
    }

    /**
     * @throws Throwable
     */
    public function get(): BackupConfiguration
    {
        $response = $this->connector->send(new GetBackupConfiguration($this->organization, $this->serverId, $this->backupConfigurationId));

        /** @var array<array-key, mixed> $data */
        $data = $response->json('data');

        return BackupConfiguration::from($data);
    }

    /**
     * Update the backup configuration. Forge returns 202 with an empty body,
     * so this returns void; call {@see get()} afterwards to read the new
     * state.
     *
     * Note: the spec marks `storage_provider_id`, `frequency`, `retention`,
     * and `database_ids` as required on update — this is effectively a full
     * replacement, not a partial patch.
     *
     * @throws Throwable
     */
    public function update(UpdateBackupConfigurationData $data): void
    {
        $this->connector->send(new UpdateBackupConfiguration($this->organization, $this->serverId, $this->backupConfigurationId, $data));
    }

    /**
     * @throws Throwable
     */
    public function delete(): void
    {
        $this->connector->send(new DeleteBackupConfiguration($this->organization, $this->serverId, $this->backupConfigurationId));
    }
}
