<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Requests\Servers;

use Override;
use PhpDevKits\ForgeSdk\Data\ListBackupInstancesOptions;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetBackupInstances extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $organization,
        private readonly int|string $serverId,
        private readonly int|string $backupConfigurationId,
        private readonly ?ListBackupInstancesOptions $options = null,
    ) {}

    #[Override]
    public function resolveEndpoint(): string
    {
        return sprintf(
            '/orgs/%s/servers/%s/database/backups/%s/instances',
            $this->organization,
            $this->serverId,
            $this->backupConfigurationId,
        );
    }

    /**
     * @return array<string, int|string>
     */
    #[Override]
    protected function defaultQuery(): array
    {
        return $this->options?->toQuery() ?? [];
    }
}
