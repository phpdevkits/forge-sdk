<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

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
}
