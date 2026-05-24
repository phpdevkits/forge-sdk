<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use PhpDevKits\ForgeSdk\Data\Deployment;
use PhpDevKits\ForgeSdk\Requests\Servers\GetDeployment;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class DeploymentResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        private readonly string $organization,
        private readonly int|string $serverId,
        private readonly int|string $siteId,
        private readonly int|string $deploymentId,
    ) {
        parent::__construct($connector);
    }

    /**
     * @throws Throwable
     */
    public function get(): Deployment
    {
        $response = $this->connector->send(
            new GetDeployment($this->organization, $this->serverId, $this->siteId, $this->deploymentId),
        );

        /** @var array<array-key, mixed> $data */
        $data = $response->json('data');

        return Deployment::from($data);
    }
}
