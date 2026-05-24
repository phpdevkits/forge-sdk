<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

final readonly class ListScheduledJobsOptions
{
    public function __construct(
        public ?int $size = null,
        public ?string $cursor = null,
        public ?string $sort = null,
        public ?string $status = null,
        public ?string $user = null,
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

        if ($this->status !== null) {
            $query['filter[status]'] = $this->status;
        }

        if ($this->user !== null) {
            $query['filter[user]'] = $this->user;
        }

        return $query;
    }
}
