<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

final readonly class ListSitesOptions
{
    public function __construct(
        public ?int $size = null,
        public ?string $cursor = null,
        public ?string $sort = null,
        public ?string $name = null,
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

        if ($this->name !== null) {
            $query['filter[name]'] = $this->name;
        }

        return $query;
    }
}
