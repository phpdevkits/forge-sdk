<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use Generator;
use PhpDevKits\ForgeSdk\Data\BackupConfiguration;
use PhpDevKits\ForgeSdk\Data\CreateBackupConfigurationData;
use PhpDevKits\ForgeSdk\Data\ListBackupConfigurationsOptions;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Requests\Servers\CreateBackupConfiguration;
use PhpDevKits\ForgeSdk\Requests\Servers\GetBackupConfigurations;
use PhpDevKits\ForgeSdk\Resources\Concerns\ParsesPage;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

// NOTE: POST /database/backups returns 202 Accepted with an empty body —
// Forge installs the backup configuration asynchronously. The endpoint does
// NOT return the freshly-created `BackupConfigurationResource`. Callers
// wanting the created DTO should call $this->all() / $this->iterate() after
// create and match by name.

final class BackupConfigurationsResource extends BaseResource
{
    use ParsesPage;

    public function __construct(
        Connector $connector,
        private readonly string $organization,
        private readonly int|string $serverId,
    ) {
        parent::__construct($connector);
    }

    /**
     * @return Page<BackupConfiguration>
     *
     * @throws Throwable
     */
    public function all(?ListBackupConfigurationsOptions $options = null): Page
    {
        return $this->parsePage(
            $this->connector->send(new GetBackupConfigurations($this->organization, $this->serverId, $options)),
            BackupConfiguration::from(...),
        );
    }

    /**
     * @return Generator<int, BackupConfiguration>
     *
     * @throws Throwable
     */
    public function iterate(?ListBackupConfigurationsOptions $options = null): Generator
    {
        $options ??= new ListBackupConfigurationsOptions;

        do {
            $page = $this->all($options);

            yield from $page;

            $options = new ListBackupConfigurationsOptions(
                size: $options->size,
                cursor: $page->nextCursor,
                sort: $options->sort,
                name: $options->name,
                status: $options->status,
            );
        } while ($page->hasMore());
    }

    /**
     * Create a new backup configuration on the server.
     *
     * Forge processes the install asynchronously and returns 202 Accepted
     * with an empty body — there's no `BackupConfigurationResource` to
     * hydrate. To retrieve the created configuration, call {@see all()} /
     * {@see iterate()} afterwards and match by name.
     *
     * @throws Throwable
     */
    public function create(CreateBackupConfigurationData $data): void
    {
        $this->connector->send(new CreateBackupConfiguration($this->organization, $this->serverId, $data));
    }
}
