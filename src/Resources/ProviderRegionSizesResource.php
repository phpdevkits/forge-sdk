<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use Generator;
use PhpDevKits\ForgeSdk\Data\ListProviderRegionSizesOptions;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Data\ProviderSize;
use PhpDevKits\ForgeSdk\Requests\Providers\GetProviderRegionSizes;
use PhpDevKits\ForgeSdk\Resources\Concerns\ParsesPage;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class ProviderRegionSizesResource extends BaseResource
{
    use ParsesPage;

    public function __construct(
        Connector $connector,
        private readonly int|string $providerId,
        private readonly int|string $regionId,
    ) {
        parent::__construct($connector);
    }

    /**
     * @return Page<ProviderSize>
     *
     * @throws Throwable
     */
    public function all(?ListProviderRegionSizesOptions $options = null): Page
    {
        return $this->parsePage(
            $this->connector->send(new GetProviderRegionSizes($this->providerId, $this->regionId, $options)),
            ProviderSize::from(...),
        );
    }

    /**
     * @return Generator<int, ProviderSize>
     *
     * @throws Throwable
     */
    public function iterate(?ListProviderRegionSizesOptions $options = null): Generator
    {
        $options ??= new ListProviderRegionSizesOptions;

        do {
            $page = $this->all($options);

            yield from $page;

            $options = new ListProviderRegionSizesOptions(
                size: $options->size,
                cursor: $page->nextCursor,
            );
        } while ($page->hasMore());
    }
}
