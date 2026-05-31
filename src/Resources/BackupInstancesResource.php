<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use Generator;
use PhpDevKits\ForgeSdk\Data\BackupInstance;
use PhpDevKits\ForgeSdk\Data\ListBackupInstancesOptions;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Requests\Servers\GetBackupInstances;
use PhpDevKits\ForgeSdk\Requests\Servers\TriggerBackup;
use PhpDevKits\ForgeSdk\Resources\Concerns\ParsesPage;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class BackupInstancesResource extends BaseResource
{
    use ParsesPage;

    public function __construct(
        Connector $connector,
        private readonly string $organization,
        private readonly int|string $serverId,
        private readonly int|string $backupConfigurationId,
    ) {
        parent::__construct($connector);
    }

    /**
     * @return Page<BackupInstance>
     *
     * @throws Throwable
     */
    public function all(?ListBackupInstancesOptions $options = null): Page
    {
        return $this->parsePage(
            $this->connector->send(new GetBackupInstances($this->organization, $this->serverId, $this->backupConfigurationId, $options)),
            BackupInstance::from(...),
        );
    }

    /**
     * @return Generator<int, BackupInstance>
     *
     * @throws Throwable
     */
    public function iterate(?ListBackupInstancesOptions $options = null): Generator
    {
        $options ??= new ListBackupInstancesOptions;

        do {
            $page = $this->all($options);

            yield from $page;

            $options = new ListBackupInstancesOptions(
                size: $options->size,
                cursor: $page->nextCursor,
                sort: $options->sort,
                status: $options->status,
            );
        } while ($page->hasMore());
    }

    /**
     * Manually trigger a new backup. Forge processes this asynchronously and is
     * documented to return 202; the resource returns the hydrated BackupInstance
     * from the response body when one is provided.
     *
     * @throws Throwable
     */
    public function trigger(): BackupInstance
    {
        $response = $this->connector->send(new TriggerBackup($this->organization, $this->serverId, $this->backupConfigurationId));

        /** @var array<array-key, mixed> $body */
        $body = $response->json('data');

        return BackupInstance::from($body);
    }
}
