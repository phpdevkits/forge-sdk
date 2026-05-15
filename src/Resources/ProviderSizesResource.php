<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use Generator;
use PhpDevKits\ForgeSdk\Data\ListProviderSizesOptions;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Data\ProviderSize;
use PhpDevKits\ForgeSdk\Requests\Providers\GetProviderSizes;
use PhpDevKits\ForgeSdk\Resources\Concerns\ParsesPage;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class ProviderSizesResource extends BaseResource
{
    use ParsesPage;

    public function __construct(
        Connector $connector,
        private readonly int|string $providerId,
    ) {
        parent::__construct($connector);
    }

    /**
     * @return Page<ProviderSize>
     *
     * @throws Throwable
     */
    public function all(?ListProviderSizesOptions $options = null): Page
    {
        return $this->parsePage(
            $this->connector->send(new GetProviderSizes($this->providerId, $options)),
            ProviderSize::from(...),
        );
    }

    /**
     * @return Generator<int, ProviderSize>
     *
     * @throws Throwable
     */
    public function iterate(?ListProviderSizesOptions $options = null): Generator
    {
        $options ??= new ListProviderSizesOptions;

        do {
            $page = $this->all($options);

            yield from $page;

            $options = new ListProviderSizesOptions(
                size: $options->size,
                cursor: $page->nextCursor,
            );
        } while ($page->hasMore());
    }
}
