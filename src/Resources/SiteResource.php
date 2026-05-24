<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use PhpDevKits\ForgeSdk\Data\Deployment;
use PhpDevKits\ForgeSdk\Data\Site;
use PhpDevKits\ForgeSdk\Data\UpdateSiteData;
use PhpDevKits\ForgeSdk\Requests\Servers\DeleteSite;
use PhpDevKits\ForgeSdk\Requests\Servers\GetSite;
use PhpDevKits\ForgeSdk\Requests\Servers\UpdateSite;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class SiteResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        private readonly string $organization,
        private readonly int|string $serverId,
        private readonly int|string $siteId,
    ) {
        parent::__construct($connector);
    }

    /**
     * @throws Throwable
     */
    public function get(): Site
    {
        $response = $this->connector->send(new GetSite($this->organization, $this->siteId));

        /** @var array<array-key, mixed> $data */
        $data = $response->json('data');

        return Site::from($data);
    }

    /**
     * Update the site. Forge returns 202 with an empty body, so this returns
     * void; call {@see get()} afterwards to read the updated state.
     *
     * @throws Throwable
     */
    public function update(UpdateSiteData $data): void
    {
        $this->connector->send(new UpdateSite($this->organization, $this->serverId, $this->siteId, $data));
    }

    /**
     * @throws Throwable
     */
    public function delete(): void
    {
        $this->connector->send(new DeleteSite($this->organization, $this->serverId, $this->siteId));
    }

    public function deployments(): DeploymentsResource
    {
        return new DeploymentsResource($this->connector, $this->organization, $this->serverId, $this->siteId);
    }

    public function deployment(int|string $deploymentId): DeploymentResource
    {
        return new DeploymentResource($this->connector, $this->organization, $this->serverId, $this->siteId, $deploymentId);
    }

    /**
     * Convenience: trigger a deployment directly. Sugar over
     * `deployments()->trigger()`.
     *
     * @throws Throwable
     */
    public function deploy(): Deployment
    {
        return $this->deployments()->trigger();
    }

    public function deploymentScript(): DeploymentScriptResource
    {
        return new DeploymentScriptResource($this->connector, $this->organization, $this->serverId, $this->siteId);
    }

    public function environment(): EnvironmentResource
    {
        return new EnvironmentResource($this->connector, $this->organization, $this->serverId, $this->siteId);
    }

    public function nginxConfig(): NginxConfigResource
    {
        return new NginxConfigResource($this->connector, $this->organization, $this->serverId, $this->siteId);
    }
}
