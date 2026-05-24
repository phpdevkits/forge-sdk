<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use PhpDevKits\ForgeSdk\Data\NginxConfig;
use PhpDevKits\ForgeSdk\Data\UpdateNginxConfigData;
use PhpDevKits\ForgeSdk\Requests\Servers\GetNginxConfig;
use PhpDevKits\ForgeSdk\Requests\Servers\UpdateNginxConfig;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class NginxConfigResource extends BaseResource
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
    public function get(): NginxConfig
    {
        $response = $this->connector->send(new GetNginxConfig($this->organization, $this->serverId, $this->siteId));

        /** @var array<array-key, mixed> $data */
        $data = $response->json('data');

        return NginxConfig::from($data);
    }

    /**
     * Update the site's Nginx configuration. Forge processes this
     * asynchronously and returns 202 with an empty body, so this returns void;
     * call {@see get()} afterwards to read the regenerated config.
     *
     * @throws Throwable
     */
    public function update(UpdateNginxConfigData $data): void
    {
        $this->connector->send(
            new UpdateNginxConfig($this->organization, $this->serverId, $this->siteId, $data),
        );
    }
}
