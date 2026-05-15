<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\Page;

test('exposes the items, cursors, and size from the constructor',
    function (): void {
        $page = new Page(
            data: ['a', 'b', 'c'],
            nextCursor: 'next-123',
            prevCursor: 'prev-456',
            size: 3,
        );

        expect($page->data)->toBe(['a', 'b', 'c'])
            ->and($page->nextCursor)->toBe('next-123')
            ->and($page->prevCursor)->toBe('prev-456')
            ->and($page->size)->toBe(3);
    });

test('hasMore() is true when nextCursor is set',
    function (): void {
        $page = new Page(data: ['a'], nextCursor: 'next', prevCursor: null, size: 1);

        expect($page->hasMore())->toBeTrue();
    });

test('hasMore() is false when nextCursor is null',
    function (): void {
        $page = new Page(data: ['a'], nextCursor: null, prevCursor: null, size: 1);

        expect($page->hasMore())->toBeFalse();
    });

test('iterates over the data items',
    function (): void {
        $page = new Page(data: ['x', 'y', 'z'], nextCursor: null, prevCursor: null, size: 3);

        $collected = [];
        foreach ($page as $item) {
            $collected[] = $item;
        }

        expect($collected)->toBe(['x', 'y', 'z']);
    });

test('iterates over an empty page without yielding anything',
    function (): void {
        $page = new Page(data: [], nextCursor: null, prevCursor: null, size: 0);

        $collected = [];
        foreach ($page as $item) {
            $collected[] = $item;
        }

        expect($collected)->toBe([]);
    });

test('jsonSerialize() emits the snake-case shape',
    function (): void {
        $page = new Page(
            data: ['a', 'b'],
            nextCursor: 'next',
            prevCursor: 'prev',
            size: 2,
        );

        expect($page->jsonSerialize())->toBe([
            'data' => ['a', 'b'],
            'next_cursor' => 'next',
            'prev_cursor' => 'prev',
            'size' => 2,
        ]);
    });
