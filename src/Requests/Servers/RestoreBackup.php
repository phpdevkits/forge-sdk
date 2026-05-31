<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Requests\Servers;

use Override;
use PhpDevKits\ForgeSdk\Data\RestoreBackupData;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class RestoreBackup extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        private readonly string $organization,
        private readonly int|string $serverId,
        private readonly int|string $backupConfigurationId,
        private readonly int|string $backupId,
        private readonly RestoreBackupData $data,
    ) {}

    #[Override]
    public function resolveEndpoint(): string
    {
        return sprintf(
            '/orgs/%s/servers/%s/database/backups/%s/instances/%s/restores',
            $this->organization,
            $this->serverId,
            $this->backupConfigurationId,
            $this->backupId,
        );
    }

    /**
     * @return array{database_id: int}
     */
    protected function defaultBody(): array
    {
        return $this->data->toArray();
    }
}
