<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use PhpDevKits\ForgeSdk\Data\UpdateEnvironmentData;
use PhpDevKits\ForgeSdk\Requests\Servers\GetEnvironment;
use PhpDevKits\ForgeSdk\Requests\Servers\UpdateEnvironment;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class EnvironmentResource extends BaseResource
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
     * Read the raw `.env` content for the site.
     *
     * @throws Throwable
     */
    public function get(): string
    {
        $response = $this->connector->send(new GetEnvironment($this->organization, $this->serverId, $this->siteId));

        $content = $response->json('data.attributes.content');

        return is_string($content) ? $content : '';
    }

    /**
     * Update the site's `.env` content. Forge returns 202 with an empty body,
     * so this returns void; call {@see get()} afterwards to read the new state.
     *
     * @throws Throwable
     */
    public function update(UpdateEnvironmentData $data): void
    {
        $this->connector->send(
            new UpdateEnvironment($this->organization, $this->serverId, $this->siteId, $data),
        );
    }
}
