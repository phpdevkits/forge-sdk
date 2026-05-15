<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use Generator;
use PhpDevKits\ForgeSdk\Data\ListProvidersOptions;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Data\Provider;
use PhpDevKits\ForgeSdk\Requests\Providers\GetProviders;
use PhpDevKits\ForgeSdk\Resources\Concerns\ParsesPage;
use Saloon\Http\BaseResource;
use Throwable;

final class ProvidersResource extends BaseResource
{
    use ParsesPage;

    /**
     * @return Page<Provider>
     *
     * @throws Throwable
     */
    public function all(?ListProvidersOptions $options = null): Page
    {
        return $this->parsePage(
            $this->connector->send(new GetProviders($options)),
            Provider::from(...),
        );
    }

    /**
     * @return Generator<int, Provider>
     *
     * @throws Throwable
     */
    public function iterate(?ListProvidersOptions $options = null): Generator
    {
        $options ??= new ListProvidersOptions;

        do {
            $page = $this->all($options);

            yield from $page;

            $options = new ListProvidersOptions(
                size: $options->size,
                cursor: $page->nextCursor,
            );
        } while ($page->hasMore());
    }
}
