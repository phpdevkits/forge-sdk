<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

final readonly class ListMonitorsOptions
{
    public function __construct(
        public ?int $size = null,
        public ?string $cursor = null,
        public ?string $status = null,
        public ?string $state = null,
        public ?string $type = null,
        public ?string $notify = null,
        public ?string $sort = null,
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

        if ($this->status !== null) {
            $query['filter[status]'] = $this->status;
        }

        if ($this->state !== null) {
            $query['filter[state]'] = $this->state;
        }

        if ($this->type !== null) {
            $query['filter[type]'] = $this->type;
        }

        if ($this->notify !== null) {
            $query['filter[notify]'] = $this->notify;
        }

        if ($this->sort !== null) {
            $query['sort'] = $this->sort;
        }

        return $query;
    }
}
