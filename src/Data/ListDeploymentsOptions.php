<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

final readonly class ListDeploymentsOptions
{
    public function __construct(
        public ?int $size = null,
        public ?string $cursor = null,
        public ?string $sort = null,
        public ?string $commitHash = null,
        public ?string $commitMessage = null,
        public ?string $commitAuthor = null,
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

        if ($this->commitHash !== null) {
            $query['filter[commit_hash]'] = $this->commitHash;
        }

        if ($this->commitMessage !== null) {
            $query['filter[commit_message]'] = $this->commitMessage;
        }

        if ($this->commitAuthor !== null) {
            $query['filter[commit_author]'] = $this->commitAuthor;
        }

        return $query;
    }
}
