<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Requests\Sites;

use Override;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class DeleteScheduledJob extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        private readonly string $organization,
        private readonly int|string $serverId,
        private readonly int|string $siteId,
        private readonly int|string $jobId,
    ) {}

    #[Override]
    public function resolveEndpoint(): string
    {
        return sprintf('/orgs/%s/servers/%s/sites/%s/scheduled-jobs/%s', $this->organization, $this->serverId, $this->siteId, $this->jobId);
    }
}
