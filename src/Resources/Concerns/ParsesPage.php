<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources\Concerns;

use PhpDevKits\ForgeSdk\Data\Page;
use Saloon\Http\Response;

trait ParsesPage
{
    /**
     * Parse a Forge JSON:API paginated response into a `Page<T>`.
     *
     * @template T of object
     *
     * @param  callable(array<array-key, mixed>): T  $hydrator  Maps a JSON:API resource object to a typed DTO.
     * @return Page<T>
     */
    private function parsePage(Response $response, callable $hydrator): Page
    {
        $data = $response->json('data');
        $meta = $response->json('meta');

        $items = [];
        if (is_array($data)) {
            foreach ($data as $entry) {
                if (is_array($entry)) {
                    $items[] = $hydrator($entry);
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
