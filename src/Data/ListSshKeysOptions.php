<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

final readonly class ListSshKeysOptions
{
    public function __construct(
        public ?int $size = null,
        public ?string $cursor = null,
        public ?string $name = null,
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

        if ($this->name !== null) {
            $query['filter[name]'] = $this->name;
        }

        if ($this->user !== null) {
            $query['filter[user]'] = $this->user;
        }

        return $query;
    }
}
