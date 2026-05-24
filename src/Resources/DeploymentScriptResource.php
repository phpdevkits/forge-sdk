<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use PhpDevKits\ForgeSdk\Data\DeploymentScript;
use PhpDevKits\ForgeSdk\Data\UpdateDeploymentScriptData;
use PhpDevKits\ForgeSdk\Requests\Servers\GetDeploymentScript;
use PhpDevKits\ForgeSdk\Requests\Servers\UpdateDeploymentScript;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class DeploymentScriptResource extends BaseResource
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
    public function get(): DeploymentScript
    {
        $response = $this->connector->send(new GetDeploymentScript($this->organization, $this->serverId, $this->siteId));

        /** @var array<array-key, mixed> $data */
        $data = $response->json('data');

        return DeploymentScript::from($data);
    }

    /**
     * @throws Throwable
     */
    public function update(UpdateDeploymentScriptData $data): DeploymentScript
    {
        $response = $this->connector->send(
            new UpdateDeploymentScript($this->organization, $this->serverId, $this->siteId, $data),
        );

        /** @var array<array-key, mixed> $body */
        $body = $response->json('data');

        return DeploymentScript::from($body);
    }
}
