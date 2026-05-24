<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Requests\Servers;

use Override;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetScheduledJobOutput extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $organization,
        private readonly int|string $serverId,
        private readonly int|string $jobId,
    ) {}

    #[Override]
    public function resolveEndpoint(): string
    {
        return sprintf('/orgs/%s/servers/%s/scheduled-jobs/%s/output', $this->organization, $this->serverId, $this->jobId);
    }
}
