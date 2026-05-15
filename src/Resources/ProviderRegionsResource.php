<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use Generator;
use PhpDevKits\ForgeSdk\Data\ListProviderRegionsOptions;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Data\ProviderRegion;
use PhpDevKits\ForgeSdk\Requests\Providers\GetProviderRegions;
use PhpDevKits\ForgeSdk\Resources\Concerns\ParsesPage;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class ProviderRegionsResource extends BaseResource
{
    use ParsesPage;

    public function __construct(
        Connector $connector,
        private readonly int|string $providerId,
    ) {
        parent::__construct($connector);
    }

    /**
     * @return Page<ProviderRegion>
     *
     * @throws Throwable
     */
    public function all(?ListProviderRegionsOptions $options = null): Page
    {
        return $this->parsePage(
            $this->connector->send(new GetProviderRegions($this->providerId, $options)),
            ProviderRegion::from(...),
        );
    }

    /**
     * @return Generator<int, ProviderRegion>
     *
     * @throws Throwable
     */
    public function iterate(?ListProviderRegionsOptions $options = null): Generator
    {
        $options ??= new ListProviderRegionsOptions;

        do {
            $page = $this->all($options);

            yield from $page;

            $options = new ListProviderRegionsOptions(
                size: $options->size,
                cursor: $page->nextCursor,
            );
        } while ($page->hasMore());
    }
}
