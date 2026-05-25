<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Requests\Servers;

use Override;
use PhpDevKits\ForgeSdk\Enums\SiteLog;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class DeleteSiteLog extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        private readonly string $organization,
        private readonly int|string $serverId,
        private readonly int|string $siteId,
        private readonly SiteLog $log,
    ) {}

    #[Override]
    public function resolveEndpoint(): string
    {
        return sprintf('/orgs/%s/servers/%s/sites/%s/logs/%s', $this->organization, $this->serverId, $this->siteId, $this->log->value);
    }
}
