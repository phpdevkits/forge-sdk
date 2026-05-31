<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Requests\Servers;

use Override;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class TriggerBackup extends Request
{
    protected Method $method = Method::POST;

    public function __construct(
        private readonly string $organization,
        private readonly int|string $serverId,
        private readonly int|string $backupConfigurationId,
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
}
