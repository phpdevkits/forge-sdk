<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use Generator;
use PhpDevKits\ForgeSdk\Data\ListOrganizationsOptions;
use PhpDevKits\ForgeSdk\Data\Organization;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Requests\Organizations\GetOrganizations;
use Saloon\Http\BaseResource;
use Saloon\Http\Response;

final class OrganizationsResource extends BaseResource
{
    /**
     * @return Page<Organization>
     *
     * @throws \Throwable
     */
    public function all(?ListOrganizationsOptions $options = null): Page
    {
        return $this->parsePage(
            $this->connector->send(new GetOrganizations($options)),
        );
    }

    /**
     * @return Generator<int, Organization>
     *
     * @throws \Throwable
     */
    public function iterate(?ListOrganizationsOptions $options = null): Generator
    {
        $options ??= new ListOrganizationsOptions;

        do {
            $page = $this->all($options);

            foreach ($page as $organization) {
                yield $organization;
            }

            $options = new ListOrganizationsOptions(
                size: $options->size,
                cursor: $page->nextCursor,
            );
        } while ($page->hasMore());
    }

    /**
     * @return Page<Organization>
     */
    private function parsePage(Response $response): Page
    {
        $data = $response->json('data');
        $meta = $response->json('meta');

        $items = [];
        if (is_array($data)) {
            foreach ($data as $entry) {
                if (is_array($entry)) {
                    $items[] = Organization::from($entry);
                }
            }
        }

        $nextCursor = null;
        $prevCursor = null;
        $size = count($items);

        if (is_array($meta)) {
            $rawNext = $meta['next_cursor'] ?? null;
            if (is_string($rawNext)) {
                $nextCursor = $rawNext;
            }

            $rawPrev = $meta['prev_cursor'] ?? null;
            if (is_string($rawPrev)) {
                $prevCursor = $rawPrev;
            }

            $rawSize = $meta['per_page'] ?? null;
            if (is_int($rawSize)) {
                $size = $rawSize;
            }
        }

        return new Page(
            data: $items,
            nextCursor: $nextCursor,
            prevCursor: $prevCursor,
            size: $size,
        );
    }
}
