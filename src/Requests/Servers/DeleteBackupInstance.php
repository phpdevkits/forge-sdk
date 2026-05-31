<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Requests\Servers;

use Override;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class DeleteBackupInstance extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        private readonly string $organization,
        private readonly int|string $serverId,
        private readonly int|string $backupConfigurationId,
        private readonly int|string $backupId,
    ) {}

    #[Override]
    public function resolveEndpoint(): string
    {
        return sprintf(
            '/orgs/%s/servers/%s/database/backups/%s/instances/%s',
            $this->organization,
            $this->serverId,
            $this->backupConfigurationId,
            $this->backupId,
        );
    }
}
