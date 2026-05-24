<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use Generator;
use PhpDevKits\ForgeSdk\Data\CreateSiteData;
use PhpDevKits\ForgeSdk\Data\ListSitesOptions;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Data\Site;
use PhpDevKits\ForgeSdk\Requests\Servers\CreateSite;
use PhpDevKits\ForgeSdk\Requests\Servers\GetSites;
use PhpDevKits\ForgeSdk\Resources\Concerns\ParsesPage;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class SitesResource extends BaseResource
{
    use ParsesPage;

    public function __construct(
        Connector $connector,
        private readonly string $organization,
        private readonly int|string $serverId,
    ) {
        parent::__construct($connector);
    }

    /**
     * @return Page<Site>
     *
     * @throws Throwable
     */
    public function all(?ListSitesOptions $options = null): Page
    {
        return $this->parsePage(
            $this->connector->send(new GetSites($this->organization, $this->serverId, $options)),
            Site::from(...),
        );
    }

    /**
     * @return Generator<int, Site>
     *
     * @throws Throwable
     */
    public function iterate(?ListSitesOptions $options = null): Generator
    {
        $options ??= new ListSitesOptions;

        do {
            $page = $this->all($options);

            yield from $page;

            $options = new ListSitesOptions(
                size: $options->size,
                cursor: $page->nextCursor,
                sort: $options->sort,
                name: $options->name,
            );
        } while ($page->hasMore());
    }

    /**
     * Create a new site. Returns the hydrated Site from the 202 response body.
     *
     * @throws Throwable
     */
    public function create(CreateSiteData $data): Site
    {
        $response = $this->connector->send(new CreateSite($this->organization, $this->serverId, $data));

        /** @var array<array-key, mixed> $body */
        $body = $response->json('data');

        return Site::from($body);
    }
}
