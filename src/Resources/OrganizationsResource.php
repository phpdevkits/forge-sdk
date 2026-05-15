<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use Generator;
use PhpDevKits\ForgeSdk\Data\ListOrganizationsOptions;
use PhpDevKits\ForgeSdk\Data\Organization;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Requests\Organizations\GetOrganizations;
use PhpDevKits\ForgeSdk\Resources\Concerns\ParsesPage;
use Saloon\Http\BaseResource;
use Throwable;

final class OrganizationsResource extends BaseResource
{
    use ParsesPage;

    /**
     * @return Page<Organization>
     *
     * @throws Throwable
     */
    public function all(?ListOrganizationsOptions $options = null): Page
    {
        return $this->parsePage(
            $this->connector->send(new GetOrganizations($options)),
            Organization::from(...),
        );
    }

    /**
     * @return Generator<int, Organization>
     *
     * @throws Throwable
     */
    public function iterate(?ListOrganizationsOptions $options = null): Generator
    {
        $options ??= new ListOrganizationsOptions;

        do {
            $page = $this->all($options);

            yield from $page;

            $options = new ListOrganizationsOptions(
                size: $options->size,
                cursor: $page->nextCursor,
            );
        } while ($page->hasMore());
    }
}
