<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Requests\Servers;

use Override;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetDeployment extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        private readonly string $organization,
        private readonly int|string $serverId,
        private readonly int|string $siteId,
        private readonly int|string $deploymentId,
    ) {}

    #[Override]
    public function resolveEndpoint(): string
    {
        return sprintf(
            '/orgs/%s/servers/%s/sites/%s/deployments/%s',
            $this->organization,
            $this->serverId,
            $this->siteId,
            $this->deploymentId,
        );
    }
}
