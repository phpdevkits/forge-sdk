<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use Generator;
use IteratorAggregate;
use JsonSerializable;
use Override;

/**
 * @template T
 *
 * @implements IteratorAggregate<int, T>
 */
final readonly class Page implements IteratorAggregate, JsonSerializable
{
    /**
     * @param  list<T>  $data
     */
    public function __construct(
        public array $data,
        public ?string $nextCursor,
        public ?string $prevCursor,
        public int $size,
    ) {}

    public function hasMore(): bool
    {
        return $this->nextCursor !== null;
    }

    /**
     * @return Generator<int, T>
     */
    #[Override]
    public function getIterator(): Generator
    {
        foreach ($this->data as $key => $item) {
            yield $key => $item;
        }
    }

    /**
     * @return array{data: list<T>, next_cursor: ?string, prev_cursor: ?string, size: int}
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'data' => $this->data,
            'next_cursor' => $this->nextCursor,
            'prev_cursor' => $this->prevCursor,
            'size' => $this->size,
        ];
    }
}
