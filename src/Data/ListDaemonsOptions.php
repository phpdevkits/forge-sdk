<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

final readonly class ListDaemonsOptions
{
    public function __construct(
        public ?int $size = null,
        public ?string $cursor = null,
        public ?string $sort = null,
        public ?string $user = null,
        public ?int $siteId = null,
        public ?string $directory = null,
    ) {}

    /**
     * @return array<string, int|string>
     */
    public function toQuery(): array
    {
        $query = [];

        if ($this->size !== null) {
            $query['page[size]'] = $this->size;
        }

        if ($this->cursor !== null) {
            $query['page[cursor]'] = $this->cursor;
        }

        if ($this->sort !== null) {
            $query['sort'] = $this->sort;
        }

        if ($this->user !== null) {
            $query['filter[user]'] = $this->user;
        }

        if ($this->siteId !== null) {
            $query['filter[site_id]'] = $this->siteId;
        }

        if ($this->directory !== null) {
            $query['filter[directory]'] = $this->directory;
        }

        return $query;
    }
}
