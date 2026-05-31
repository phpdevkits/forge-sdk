<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Requests\Sites;

use Override;
use PhpDevKits\ForgeSdk\Data\ListScheduledJobsOptions;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetScheduledJobs extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $organization,
        private readonly int|string $serverId,
        private readonly int|string $siteId,
        private readonly ?ListScheduledJobsOptions $options = null,
    ) {}

    #[Override]
    public function resolveEndpoint(): string
    {
        return sprintf('/orgs/%s/servers/%s/sites/%s/scheduled-jobs', $this->organization, $this->serverId, $this->siteId);
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
